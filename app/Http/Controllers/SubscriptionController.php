<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions and cost analytics.
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

        $metrics = [
            'total_monthly_cost' => $totalMonthlyCost,
            'total_yearly_cost' => $totalYearlyCost,
            'active_count' => $activeCount,
            'inactive_count' => $inactiveCount,
            'renewing_soon_count' => $renewingSoonCount,
            'overdue_count' => $overdueCount,
        ];

        return view('dashboard', compact(
            'subscriptions',
            'categories',
            'paymentMethods',
            'metrics',
            'categoryBreakdown'
        ));
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
            'currency' => 'nullable|string|max:10',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly',
            'next_billing_date' => 'required|date',
            'is_active' => 'nullable',
        ]);

        $validated['currency'] = $validated['currency'] ?? 'IDR';
        $validated['is_active'] = $request->boolean('is_active', true);

        $subscription = Subscription::create($validated);

        return redirect()->route('subscriptions.index')
            ->with('success', "Langganan '{$subscription->service_name}' berhasil ditambahkan.");
    }

    /**
     * Toggle the active status of the subscription.
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->is_active = !$subscription->is_active;
        $subscription->save();

        $statusText = $subscription->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('subscriptions.index')
            ->with('success', "Status langganan '{$subscription->service_name}' berhasil {$statusText}.");
    }

    /**
     * Remove the specified subscription from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $subscription = Subscription::findOrFail($id);
        $serviceName = $subscription->service_name;
        $subscription->delete();

        return redirect()->route('subscriptions.index')
            ->with('success', "Langganan '{$serviceName}' berhasil dihapus.");
    }
}
