<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkDestroySubscriptionRequest;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Subscription;
use App\Services\SubscriptionCostService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Inject domain cost analysis service.
     */
    public function __construct(
        protected SubscriptionCostService $costService
    ) {}

    /**
     * Display a listing of subscriptions, cost analytics, and payment breakdowns.
     */
    public function index(): View
    {
        return view('dashboard', $this->costService->getDashboardData());
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
     * Store a newly created subscription in storage.
     */
    public function store(StoreSubscriptionRequest $request): RedirectResponse
    {
        $subscription = Subscription::create($request->validatedData());

        return redirect()->back()
            ->with('success', "Langganan '{$subscription->service_name}' berhasil ditambahkan.");
    }

    /**
     * Update the specified subscription in storage.
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): RedirectResponse
    {
        $subscription->update($request->validatedData());

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
    public function bulkDestroy(BulkDestroySubscriptionRequest $request): RedirectResponse
    {
        $count = Subscription::whereIn('id', $request->validated()['ids'])->delete();

        return redirect()->back()
            ->with('success', "{$count} langganan berhasil dihapus secara massal.");
    }
}
