<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyConverter
{
    /**
     * Default exchange rates to IDR (fallback values).
     */
    public const DEFAULT_RATES = [
        'IDR' => 1.0,
        'USD' => 16250.0,
        'EUR' => 17600.0,
        'SGD' => 12550.0,
        'GBP' => 21100.0,
    ];

    /**
     * Get exchange rates with live API fetching, fallback, and 6-hour caching.
     */
    public static function getRates(): array
    {
        return Cache::remember('currency_rates_to_idr', 21600, function () {
            try {
                $response = Http::timeout(3)->get('https://open.er-api.com/v6/latest/USD');

                if ($response->successful()) {
                    $data = $response->json();
                    $rates = $data['rates'] ?? [];

                    if (isset($rates['IDR']) && (float) $rates['IDR'] > 0) {
                        $usdToIdr = (float) $rates['IDR'];
                        $eurRate = isset($rates['EUR']) && (float) $rates['EUR'] > 0 ? (float) $rates['EUR'] : null;
                        $sgdRate = isset($rates['SGD']) && (float) $rates['SGD'] > 0 ? (float) $rates['SGD'] : null;
                        $gbpRate = isset($rates['GBP']) && (float) $rates['GBP'] > 0 ? (float) $rates['GBP'] : null;

                        return [
                            'IDR' => 1.0,
                            'USD' => $usdToIdr,
                            'EUR' => $eurRate ? round($usdToIdr / $eurRate, 2) : self::DEFAULT_RATES['EUR'],
                            'SGD' => $sgdRate ? round($usdToIdr / $sgdRate, 2) : self::DEFAULT_RATES['SGD'],
                            'GBP' => $gbpRate ? round($usdToIdr / $gbpRate, 2) : self::DEFAULT_RATES['GBP'],
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal mengambil kurs valas live dari API, menggunakan nilai fallback: ' . $e->getMessage());
            }

            return self::DEFAULT_RATES;
        });
    }

    /**
     * Convert an amount in a given currency to IDR.
     */
    public static function toIdr(float $amount, ?string $currency = 'IDR'): float
    {
        $currency = strtoupper(trim($currency ?? 'IDR'));
        $rates = self::getRates();

        $rate = $rates[$currency] ?? 1.0;

        return $amount * $rate;
    }

    /**
     * Get currency symbol.
     */
    public static function getSymbol(string $currency): string
    {
        return match (strtoupper($currency)) {
            'USD' => '$',
            'EUR' => '€',
            'SGD' => 'S$',
            'GBP' => '£',
            default => 'Rp',
        };
    }
}
