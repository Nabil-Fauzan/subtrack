<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions, cost analytics, and payment breakdowns.
     */
    public function index(): View
    {
        $subscriptions = Subscription::with(['category', 'paymentMethod'])
            ->orderByDesc('is_active')
            ->orderBy('next_billing_date')
            ->get();

        $categories = Category::orderBy('name')->get();
        $paymentMethods = PaymentMethod::orderBy('name')->get();

        // Separate active subscriptions for aggregation
        $activeSubscriptions = $subscriptions->where('is_active', true);

        $totalMonthlyCost = $activeSubscriptions->sum(fn ($item) => $item->normalized_monthly_cost);
        $totalYearlyCost = $activeSubscriptions->sum(fn ($item) => $item->normalized_yearly_cost);
        $activeCount = $activeSubscriptions->count();
        $inactiveCount = $subscriptions->where('is_active', false)->count();
        $renewingSoonCount = $activeSubscriptions->where('is_renewing_soon', true)->count();
        $overdueCount = $subscriptions->where('is_overdue', true)->count();

        // Budget Cap calculation (Default Rp 3.000.000 / month)
        $budgetCap = 3000000;
        $budgetPercentage = $budgetCap > 0 ? round(($totalMonthlyCost / $budgetCap) * 100, 1) : 0;
        $isOverbudget = $totalMonthlyCost > $budgetCap;
        $budgetDiff = abs($budgetCap - $totalMonthlyCost);

        // Calculate Category Share for Visual Stacked Bar
        $categoryBreakdown = $categories->map(function ($category) use ($subscriptions, $totalMonthlyCost) {
            $catActiveSubs = $subscriptions->where('category_id', $category->id)->where('is_active', true);
            $catMonthlyCost = $catActiveSubs->sum(fn ($item) => $item->normalized_monthly_cost);
            $percentage = $totalMonthlyCost > 0 ? ($catMonthlyCost / $totalMonthlyCost) * 100 : 0;

            return [
                'id' => $category->id,
                'name' => $category->name,
                'color_hex' => $category->color_hex,
                'monthly_cost' => $catMonthlyCost,
                'percentage' => round($percentage, 1),
                'active_count' => $catActiveSubs->count(),
            ];
        })->filter(fn ($cat) => $cat['monthly_cost'] > 0)->values();

        // Calculate Payment Method Spending Breakdown & Cashflow Risk Insights
        $paymentBreakdown = $paymentMethods->map(function ($pm) use ($subscriptions, $totalMonthlyCost) {
            $pmActiveSubs = $subscriptions->where('payment_method_id', $pm->id)->where('is_active', true);
            $pmMonthlyCost = $pmActiveSubs->sum(fn ($item) => $item->normalized_monthly_cost);
            $percentage = $totalMonthlyCost > 0 ? ($pmMonthlyCost / $totalMonthlyCost) * 100 : 0;

            return [
                'id' => $pm->id,
                'name' => $pm->name,
                'monthly_cost' => $pmMonthlyCost,
                'percentage' => round($percentage, 1),
                'active_count' => $pmActiveSubs->count(),
                'is_heavy_burden' => $percentage > 40.0,
            ];
        })->filter(fn ($pm) => $pm['monthly_cost'] > 0)->sortByDesc('monthly_cost')->values();

        $metrics = [
            'total_monthly_cost' => $totalMonthlyCost,
            'total_yearly_cost' => $totalYearlyCost,
            'active_count' => $activeCount,
            'inactive_count' => $inactiveCount,
            'renewing_soon_count' => $renewingSoonCount,
            'overdue_count' => $overdueCount,
            'budget_cap' => $budgetCap,
            'budget_percentage' => $budgetPercentage,
            'is_overbudget' => $isOverbudget,
            'budget_diff' => $budgetDiff,
        ];

        return view('dashboard', compact(
            'subscriptions',
            'categories',
            'paymentMethods',
            'metrics',
            'categoryBreakdown',
            'paymentBreakdown'
        ));
    }

    /**
     * Seed realistic demo data for reviewers and showcase.
     */
    public function seedDemo(): RedirectResponse
    {
        (new DatabaseSeeder())->run();

        return redirect()->route('subscriptions.index')
            ->with('success', 'Data demo berhasil dimuat!');
    }

    /**
     * Export all subscriptions to CSV as a streamed download.
     */
    public function exportCsv(): StreamedResponse
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
     */
    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return redirect()->back()->withErrors(['csv_file' => 'Gagal membaca berkas CSV yang diunggah.']);
        }

        // Read first line to detect delimiter and header
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return redirect()->back()->withErrors(['csv_file' => 'Berkas CSV kosong atau tidak terbaca.']);
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
            return redirect()->back()->withErrors(['csv_file' => 'Format header CSV tidak valid atau berkas kosong.']);
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
            return redirect()->back()->withErrors(['csv_file' => 'Kolom "Nama Layanan" dan "Harga" wajib tersedia di header CSV.']);
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
        } catch (\Exception $e) {
            fclose($handle);
            return redirect()->back()->withErrors(['csv_file' => "Impor dibatalkan: " . $e->getMessage()]);
        }

        fclose($handle);

        return redirect()->route('subscriptions.index')
            ->with('success', "Berhasil mengimpor {$importedCount} langganan baru dari CSV!");
    }

    /**
     * Store a newly created subscription in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|in:IDR,USD,EUR,SGD,GBP',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly',
            'next_billing_date' => 'required|date',
            'is_active' => 'nullable',
        ]);

        $validated['currency'] = $validated['currency'] ?? 'IDR';
        $validated['is_active'] = $request->boolean('is_active', true);

        $subscription = Subscription::create($validated);

        return redirect()->back()
            ->with('success', "Langganan '{$subscription->service_name}' berhasil ditambahkan.");
    }

    /**
     * Update the specified subscription in storage.
     */
    public function update(Request $request, Subscription $subscription): RedirectResponse
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|in:IDR,USD,EUR,SGD,GBP',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly',
            'next_billing_date' => 'required|date',
            'is_active' => 'nullable',
        ]);

        $validated['currency'] = $validated['currency'] ?? 'IDR';
        $validated['is_active'] = $request->boolean('is_active', false);

        $subscription->update($validated);

        return redirect()->back()
            ->with('success', "Langganan '{$subscription->service_name}' berhasil diperbarui.");
    }

    /**
     * Toggle the active status of the subscription.
     */
    public function toggleStatus(Subscription $subscription): RedirectResponse
    {
        $subscription->is_active = !$subscription->is_active;
        $subscription->save();

        $statusText = $subscription->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "Status langganan '{$subscription->service_name}' berhasil {$statusText}.");
    }

    /**
     * Remove the specified subscription from storage.
     */
    public function destroy(Subscription $subscription): RedirectResponse
    {
        $serviceName = $subscription->service_name;
        $subscription->delete();

        return redirect()->back()
            ->with('success', "Langganan '{$serviceName}' berhasil dihapus.");
    }

    /**
     * Remove multiple subscriptions in bulk from storage.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|exists:subscriptions,id',
        ]);

        $count = Subscription::whereIn('id', $validated['ids'])->delete();

        return redirect()->back()
            ->with('success', "{$count} langganan berhasil dihapus secara massal.");
    }
}
