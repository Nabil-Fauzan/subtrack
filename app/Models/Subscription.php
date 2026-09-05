<?php

namespace App\Models;

use App\Services\CurrencyConverter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'payment_method_id',
        'service_name',
        'price',
        'currency',
        'billing_cycle',
        'next_billing_date',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'next_billing_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Appends calculated accessor attributes.
     */
    protected $appends = [
        'normalized_monthly_cost',
        'normalized_yearly_cost',
        'is_renewing_soon',
        'is_overdue',
        'formatted_original_price',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Accessor: Normalized monthly cost in IDR based on currency and billing cycle.
     */
    protected function normalizedMonthlyCost(): Attribute
    {
        return Attribute::make(
            get: function () {
                $rawPrice = (float) $this->price;
                $priceInIdr = CurrencyConverter::toIdr($rawPrice, $this->currency ?? 'IDR');

                return match ($this->billing_cycle) {
                    'monthly' => $priceInIdr,
                    'quarterly' => $priceInIdr / 3,
                    'yearly' => $priceInIdr / 12,
                    default => $priceInIdr,
                };
            }
        );
    }

    /**
     * Accessor: Normalized yearly cost in IDR based on currency and billing cycle.
     */
    protected function normalizedYearlyCost(): Attribute
    {
        return Attribute::make(
            get: function () {
                $rawPrice = (float) $this->price;
                $priceInIdr = CurrencyConverter::toIdr($rawPrice, $this->currency ?? 'IDR');

                return match ($this->billing_cycle) {
                    'monthly' => $priceInIdr * 12,
                    'quarterly' => $priceInIdr * 4,
                    'yearly' => $priceInIdr,
                    default => $priceInIdr * 12,
                };
            }
        );
    }

    /**
     * Accessor: Formatted original price with currency symbol.
     */
    public function getFormattedOriginalPriceAttribute(): string
    {
        $currency = strtoupper($this->currency ?? 'IDR');
        $symbol = CurrencyConverter::getSymbol($currency);

        if ($currency === 'IDR') {
            return 'Rp ' . number_format($this->price, 0, ',', '.');
        }

        return $symbol . number_format($this->price, 2, '.', ',');
    }

    /**
     * Accessor: True if next billing date is within <= 7 days from today and not overdue.
     */
    protected function isRenewingSoon(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->next_billing_date) {
                    return false;
                }
                $today = Carbon::today();
                $billingDate = Carbon::parse($this->next_billing_date)->startOfDay();

                return $billingDate->gte($today) && $billingDate->lte($today->copy()->addDays(7));
            }
        );
    }

    /**
     * Accessor: True if next billing date is before today.
     */
    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->next_billing_date) {
                    return false;
                }
                $today = Carbon::today();
                $billingDate = Carbon::parse($this->next_billing_date)->startOfDay();

                return $billingDate->lt($today);
            }
        );
    }

    /**
     * Helper to get remaining days until renewal.
     */
    public function getDaysUntilRenewalAttribute(): ?int
    {
        if (!$this->next_billing_date) {
            return null;
        }
        return (int) Carbon::today()->diffInDays(Carbon::parse($this->next_billing_date)->startOfDay(), false);
    }
}
