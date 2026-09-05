<?php

namespace App\Services;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use Illuminate\Support\Collection;

class SubscriptionCostService
{
    /**
     * Default monthly budget cap in IDR.
     */
    public const DEFAULT_BUDGET_CAP = 3000000.0;

    /**
     * Get all aggregated dashboard data for the analyzer view.
     *
     * @return array<string, mixed>
     */
    public function getDashboardData(): array
    {
        $subscriptions = Subscription::with(['category', 'paymentMethod'])
            ->orderByDesc('is_active')
            ->orderBy('next_billing_date')
            ->get();

        $categories = Category::orderBy('name')->get();
        $paymentMethods = PaymentMethod::orderBy('name')->get();

        $metrics = $this->calculateMetrics($subscriptions);
        $categoryBreakdown = $this->calculateCategoryBreakdown($categories, $subscriptions, $metrics['total_monthly_cost']);
        $paymentBreakdown = $this->calculatePaymentBreakdown($paymentMethods, $subscriptions, $metrics['total_monthly_cost']);

        return compact(
            'subscriptions',
            'categories',
            'paymentMethods',
            'metrics',
            'categoryBreakdown',
            'paymentBreakdown'
        );
    }

    /**
     * Calculate core summary metrics from subscriptions.
     *
     * @param Collection<int, Subscription> $subscriptions
     * @param float $budgetCap
     * @return array<string, mixed>
     */
    public function calculateMetrics(Collection $subscriptions, float $budgetCap = self::DEFAULT_BUDGET_CAP): array
    {
        $activeSubscriptions = $subscriptions->where('is_active', true);

        $totalMonthlyCost = (float) $activeSubscriptions->sum(fn (Subscription $item) => $item->normalized_monthly_cost);
        $totalYearlyCost = (float) $activeSubscriptions->sum(fn (Subscription $item) => $item->normalized_yearly_cost);
        $activeCount = $activeSubscriptions->count();
        $inactiveCount = $subscriptions->where('is_active', false)->count();
        $renewingSoonCount = $activeSubscriptions->where('is_renewing_soon', true)->count();
        $overdueCount = $subscriptions->where('is_overdue', true)->count();

        $budgetPercentage = $budgetCap > 0 ? round(($totalMonthlyCost / $budgetCap) * 100, 1) : 0.0;
        $isOverbudget = $totalMonthlyCost > $budgetCap;
        $budgetDiff = abs($budgetCap - $totalMonthlyCost);

        return [
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
    }

    /**
     * Calculate category spending share and distribution.
     *
     * @param Collection<int, Category> $categories
     * @param Collection<int, Subscription> $subscriptions
     * @param float $totalMonthlyCost
     * @return Collection<int, array<string, mixed>>
     */
    public function calculateCategoryBreakdown(Collection $categories, Collection $subscriptions, float $totalMonthlyCost): Collection
    {
        return $categories->map(function (Category $category) use ($subscriptions, $totalMonthlyCost) {
            $catActiveSubs = $subscriptions->where('category_id', $category->id)->where('is_active', true);
            $catMonthlyCost = (float) $catActiveSubs->sum(fn (Subscription $item) => $item->normalized_monthly_cost);
            $percentage = $totalMonthlyCost > 0 ? ($catMonthlyCost / $totalMonthlyCost) * 100 : 0.0;

            return [
                'id' => $category->id,
                'name' => $category->name,
                'color_hex' => $category->color_hex,
                'monthly_cost' => $catMonthlyCost,
                'percentage' => round($percentage, 1),
                'active_count' => $catActiveSubs->count(),
            ];
        })->filter(fn (array $cat) => $cat['monthly_cost'] > 0)->values();
    }

    /**
     * Calculate payment method spending breakdown and cashflow burden warnings.
     *
     * @param Collection<int, PaymentMethod> $paymentMethods
     * @param Collection<int, Subscription> $subscriptions
     * @param float $totalMonthlyCost
     * @return Collection<int, array<string, mixed>>
     */
    public function calculatePaymentBreakdown(Collection $paymentMethods, Collection $subscriptions, float $totalMonthlyCost): Collection
    {
        return $paymentMethods->map(function (PaymentMethod $pm) use ($subscriptions, $totalMonthlyCost) {
            $pmActiveSubs = $subscriptions->where('payment_method_id', $pm->id)->where('is_active', true);
            $pmMonthlyCost = (float) $pmActiveSubs->sum(fn (Subscription $item) => $item->normalized_monthly_cost);
            $percentage = $totalMonthlyCost > 0 ? ($pmMonthlyCost / $totalMonthlyCost) * 100 : 0.0;

            return [
                'id' => $pm->id,
                'name' => $pm->name,
                'monthly_cost' => $pmMonthlyCost,
                'percentage' => round($percentage, 1),
                'active_count' => $pmActiveSubs->count(),
                'is_heavy_burden' => $percentage > 40.0,
            ];
        })->filter(fn (array $pm) => $pm['monthly_cost'] > 0)->sortByDesc('monthly_cost')->values();
    }
}
