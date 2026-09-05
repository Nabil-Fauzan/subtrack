<?php

namespace App\Models;

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
     * Accessor: Normalized monthly cost based on billing cycle.
     */
    protected function normalizedMonthlyCost(): Attribute
    {
        return Attribute::make(
            get: function () {
                $price = (float) $this->price;
                return match ($this->billing_cycle) {
                    'monthly' => $price,
                    'quarterly' => $price / 3,
                    'yearly' => $price / 12,
                    default => $price,
                };
            }
        );
    }

    /**
     * Accessor: Normalized yearly cost based on billing cycle.
     */
    protected function normalizedYearlyCost(): Attribute
    {
        return Attribute::make(
            get: function () {
                $price = (float) $this->price;
                return match ($this->billing_cycle) {
                    'monthly' => $price * 12,
                    'quarterly' => $price * 4,
                    'yearly' => $price,
                    default => $price * 12,
                };
            }
        );
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
