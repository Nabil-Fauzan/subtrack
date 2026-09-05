<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionAnalyzerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_dashboard_page_loads_successfully_with_data()
    {
        $response = $this->get(route('subscriptions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('subscriptions');
        $response->assertViewHas('metrics');
        $response->assertViewHas('categoryBreakdown');
        $response->assertSee('SubTrack');
        $response->assertSee('GitHub Copilot');
    }

    public function test_accessors_calculate_normalized_costs_and_status()
    {
        $category = Category::first();
        $paymentMethod = PaymentMethod::first();

        // Monthly
        $monthlySub = Subscription::create([
            'service_name' => 'Monthly Service',
            'category_id' => $category->id,
            'payment_method_id' => $paymentMethod->id,
            'price' => 120000,
            'billing_cycle' => 'monthly',
            'next_billing_date' => Carbon::today()->addDays(3),
            'is_active' => true,
        ]);

        $this->assertEquals(120000, $monthlySub->normalized_monthly_cost);
        $this->assertEquals(1440000, $monthlySub->normalized_yearly_cost);
        $this->assertTrue($monthlySub->is_renewing_soon);
        $this->assertFalse($monthlySub->is_overdue);

        // Quarterly
        $quarterlySub = Subscription::create([
            'service_name' => 'Quarterly Service',
            'category_id' => $category->id,
            'payment_method_id' => $paymentMethod->id,
            'price' => 300000,
            'billing_cycle' => 'quarterly',
            'next_billing_date' => Carbon::today()->addDays(45),
            'is_active' => true,
        ]);

        $this->assertEquals(100000, $quarterlySub->normalized_monthly_cost);
        $this->assertEquals(1200000, $quarterlySub->normalized_yearly_cost);
        $this->assertFalse($quarterlySub->is_renewing_soon);

        // Yearly & Overdue
        $yearlySub = Subscription::create([
            'service_name' => 'Yearly Service',
            'category_id' => $category->id,
            'payment_method_id' => $paymentMethod->id,
            'price' => 1200000,
            'billing_cycle' => 'yearly',
            'next_billing_date' => Carbon::today()->subDays(1),
            'is_active' => true,
        ]);

        $this->assertEquals(100000, $yearlySub->normalized_monthly_cost);
        $this->assertEquals(1200000, $yearlySub->normalized_yearly_cost);
        $this->assertTrue($yearlySub->is_overdue);
        $this->assertFalse($yearlySub->is_renewing_soon);
    }

    public function test_can_create_new_subscription()
    {
        $category = Category::first();
        $paymentMethod = PaymentMethod::first();

        $payload = [
            'service_name' => 'Canva Pro',
            'category_id' => $category->id,
            'payment_method_id' => $paymentMethod->id,
            'price' => 95000,
            'billing_cycle' => 'monthly',
            'next_billing_date' => Carbon::today()->addDays(10)->toDateString(),
            'is_active' => '1',
        ];

        $response = $this->post(route('subscriptions.store'), $payload);

        $response->assertRedirect(route('subscriptions.index'));
        $this->assertDatabaseHas('subscriptions', [
            'service_name' => 'Canva Pro',
            'price' => 95000.00,
        ]);
    }

    public function test_can_toggle_subscription_status()
    {
        $sub = Subscription::first();
        $initialStatus = $sub->is_active;

        $response = $this->patch(route('subscriptions.toggle', $sub->id));

        $response->assertRedirect(route('subscriptions.index'));
        $this->assertDatabaseHas('subscriptions', [
            'id' => $sub->id,
            'is_active' => !$initialStatus,
        ]);
    }

    public function test_can_delete_subscription()
    {
        $sub = Subscription::first();
        $id = $sub->id;

        $response = $this->delete(route('subscriptions.destroy', $id));

        $response->assertRedirect(route('subscriptions.index'));
        $this->assertDatabaseMissing('subscriptions', ['id' => $id]);
    }
}
