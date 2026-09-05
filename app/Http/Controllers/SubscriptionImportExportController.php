<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportSubscriptionRequest;
use App\Services\SubscriptionCsvService;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriptionImportExportController extends Controller
{
    /**
     * Export subscriptions data as CSV.
     */
    public function exportCsv(SubscriptionCsvService $csvService): StreamedResponse
    {
        return $csvService->export();
    }

    /**
     * Import subscriptions data from uploaded CSV.
     */
    public function importCsv(ImportSubscriptionRequest $request, SubscriptionCsvService $csvService): RedirectResponse
    {
        $file = $request->file('csv_file');

        try {
            $count = $csvService->import($file);

            return redirect()->route('subscriptions.index')
                ->with('success', "Berhasil mengimpor {$count} langganan dari file CSV!");
        } catch (\Exception $e) {
            return redirect()->route('subscriptions.index')
                ->withErrors(['csv_file' => $e->getMessage()]);
        }
    }
}
