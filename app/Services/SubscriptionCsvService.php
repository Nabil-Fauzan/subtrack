<?php

namespace App\Services;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriptionCsvService
{
    /**
     * Export all subscriptions to CSV as a streamed response.
     */
    public function export(): StreamedResponse
    {
        $subscriptions = Subscription::with(['category', 'paymentMethod'])
            ->orderByDesc('is_active')
            ->orderBy('next_billing_date')
            ->get();

        $filename = 'subtrack-subscriptions-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($subscriptions) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            // CSV Columns Header
            fputcsv($file, [
                'Nama Layanan',
                'Kategori',
                'Metode Bayar',
                'Harga Asli',
                'Mata Uang',
                'Siklus Penagihan',
                'Ekuivalen Bulanan (IDR)',
                'Ekuivalen Tahunan (IDR)',
                'Jatuh Tempo Berikutnya',
                'Status',
            ]);

            foreach ($subscriptions as $sub) {
                fputcsv($file, [
                    $sub->service_name,
                    $sub->category->name ?? '-',
                    $sub->paymentMethod->name ?? '-',
                    number_format($sub->price, 2, '.', ''),
                    $sub->currency ?? 'IDR',
                    ucfirst($sub->billing_cycle),
                    number_format($sub->normalized_monthly_cost, 2, '.', ''),
                    number_format($sub->normalized_yearly_cost, 2, '.', ''),
                    $sub->next_billing_date ? Carbon::parse($sub->next_billing_date)->format('Y-m-d') : '-',
                    $sub->is_active ? 'Aktif' : 'Nonaktif',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Batch import subscriptions from CSV file with database transaction.
     *
     * @throws \Exception
     */
    public function import(UploadedFile $file): int
    {
        $filePath = $file->getRealPath();
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new \Exception('Gagal membaca berkas CSV yang diunggah.');
        }

        // Read first line to detect delimiter and header
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            throw new \Exception('Berkas CSV kosong atau tidak terbaca.');
        }

        // Strip UTF-8 BOM if present
        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);

        // Auto-detect delimiter (, ; \t)
        $semicolons = substr_count($firstLine, ';');
        $commas = substr_count($firstLine, ',');
        $tabs = substr_count($firstLine, "\t");

        $delimiter = ',';
        if ($semicolons > $commas && $semicolons >= $tabs) {
            $delimiter = ';';
        } elseif ($tabs > $commas && $tabs > $semicolons) {
            $delimiter = "\t";
        }

        $header = str_getcsv($firstLine, $delimiter);
        if (!$header || count($header) < 2) {
            fclose($handle);
            throw new \Exception('Format header CSV tidak valid atau berkas kosong.');
        }

        // Normalize header keys
        $headerMap = [];
        foreach ($header as $idx => $col) {
            $clean = strtolower(trim(str_replace(['"', "'", "\xEF\xBB\xBF"], '', $col)));
            if (str_contains($clean, 'layanan') || str_contains($clean, 'service') || str_contains($clean, 'name')) {
                $headerMap['service_name'] = $idx;
            } elseif (str_contains($clean, 'kategori') || str_contains($clean, 'category')) {
                $headerMap['category'] = $idx;
            } elseif (str_contains($clean, 'metode') || str_contains($clean, 'payment')) {
                $headerMap['payment_method'] = $idx;
            } elseif (str_contains($clean, 'harga') || str_contains($clean, 'nominal') || str_contains($clean, 'biaya') || str_contains($clean, 'price') || str_contains($clean, 'amount') || str_contains($clean, 'cost')) {
                $headerMap['price'] = $idx;
            } elseif (str_contains($clean, 'uang') || str_contains($clean, 'currency')) {
                $headerMap['currency'] = $idx;
            } elseif (str_contains($clean, 'siklus') || str_contains($clean, 'cycle')) {
                $headerMap['billing_cycle'] = $idx;
            } elseif (str_contains($clean, 'tempo') || str_contains($clean, 'date') || str_contains($clean, 'billing')) {
                $headerMap['next_billing_date'] = $idx;
            } elseif (str_contains($clean, 'status')) {
                $headerMap['is_active'] = $idx;
            }
        }

        if (!isset($headerMap['service_name']) || !isset($headerMap['price'])) {
            fclose($handle);
            throw new \Exception('Kolom "Nama Layanan" dan "Harga" wajib tersedia di header CSV.');
        }

        $importedCount = 0;
        $rowNumber = 1;

        try {
            DB::transaction(function () use ($handle, $delimiter, $headerMap, &$importedCount, &$rowNumber) {
                while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                    $rowNumber++;

                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $serviceName = trim($row[$headerMap['service_name']] ?? '');
                    if (empty($serviceName)) {
                        continue;
                    }

                    // Category
                    $catName = isset($headerMap['category']) ? trim($row[$headerMap['category']] ?? '') : 'General';
                    $catName = empty($catName) || $catName === '-' ? 'General' : $catName;
                    $category = Category::firstOrCreate(
                        ['name' => $catName],
                        ['color_hex' => '#6366F1']
                    );

                    // Payment Method
                    $pmName = isset($headerMap['payment_method']) ? trim($row[$headerMap['payment_method']] ?? '') : 'Default';
                    $pmName = empty($pmName) || $pmName === '-' ? 'Credit Card' : $pmName;
                    $paymentMethod = PaymentMethod::firstOrCreate(['name' => $pmName]);

                    // Price & Currency Parsing
                    $rawPrice = isset($headerMap['price']) ? trim($row[$headerMap['price']] ?? '0') : '0';
                    $cleanPrice = preg_replace('/[^\d.,]/', '', $rawPrice);
                    if (substr_count($cleanPrice, '.') > 0 && substr_count($cleanPrice, ',') > 0) {
                        if (strrpos($cleanPrice, ',') > strrpos($cleanPrice, '.')) {
                            $cleanPrice = str_replace('.', '', $cleanPrice);
                            $cleanPrice = str_replace(',', '.', $cleanPrice);
                        } else {
                            $cleanPrice = str_replace(',', '', $cleanPrice);
                        }
                    } elseif (substr_count($cleanPrice, ',') > 0) {
                        if (preg_match('/,\d{3}$/', $cleanPrice)) {
                            $cleanPrice = str_replace(',', '', $cleanPrice);
                        } else {
                            $cleanPrice = str_replace(',', '.', $cleanPrice);
                        }
                    }
                    $price = (float) $cleanPrice;
                    if ($price <= 0) {
                        throw new \Exception("Baris {$rowNumber}: Nilai harga '{$rawPrice}' tidak valid.");
                    }

                    $currency = isset($headerMap['currency']) ? strtoupper(trim($row[$headerMap['currency']] ?? 'IDR')) : 'IDR';
                    $currency = in_array($currency, ['IDR', 'USD', 'EUR', 'SGD', 'GBP']) ? $currency : 'IDR';

                    // Billing cycle
                    $rawCycle = isset($headerMap['billing_cycle']) ? strtolower(trim($row[$headerMap['billing_cycle']] ?? 'monthly')) : 'monthly';
                    $billingCycle = match ($rawCycle) {
                        'quarterly', 'per 3 bulan', 'triwulan' => 'quarterly',
                        'yearly', 'annual', 'tahunan' => 'yearly',
                        default => 'monthly',
                    };

                    // Next billing date
                    $rawDate = isset($headerMap['next_billing_date']) ? trim($row[$headerMap['next_billing_date']] ?? '') : '';
                    try {
                        $nextBillingDate = !empty($rawDate) && $rawDate !== '-' 
                            ? Carbon::parse($rawDate)->toDateString() 
                            : Carbon::today()->addMonth()->toDateString();
                    } catch (\Exception $e) {
                        $nextBillingDate = Carbon::today()->addMonth()->toDateString();
                    }

                    // Is Active
                    $rawStatus = isset($headerMap['is_active']) ? strtolower(trim($row[$headerMap['is_active']] ?? '1')) : '1';
                    $isActive = !in_array($rawStatus, ['0', 'false', 'nonaktif', 'inactive', 'off']);

                    Subscription::create([
                        'service_name' => $serviceName,
                        'category_id' => $category->id,
                        'payment_method_id' => $paymentMethod->id,
                        'price' => $price,
                        'currency' => $currency,
                        'billing_cycle' => $billingCycle,
                        'next_billing_date' => $nextBillingDate,
                        'is_active' => $isActive,
                    ]);

                    $importedCount++;
                }
            });
        } finally {
            fclose($handle);
        }

        return $importedCount;
    }
}
