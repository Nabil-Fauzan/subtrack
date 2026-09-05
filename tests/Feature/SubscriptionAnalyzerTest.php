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
        $response->assertSee('Pagu:');
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

        $response->assertStatus(302);
        $this->assertDatabaseHas('subscriptions', [
            'service_name' => 'Canva Pro',
            'price' => 95000.00,
        ]);
    }

    public function test_can_update_existing_subscription()
    {
        $sub = Subscription::first();
        $category = Category::latest('id')->first();
        $paymentMethod = PaymentMethod::latest('id')->first();

        $payload = [
            'service_name' => 'GitHub Copilot Enterprise',
            'category_id' => $category->id,
            'payment_method_id' => $paymentMethod->id,
            'price' => 300000,
            'billing_cycle' => 'monthly',
            'next_billing_date' => Carbon::today()->addDays(25)->toDateString(),
            'is_active' => '1',
        ];

        $response = $this->put(route('subscriptions.update', $sub), $payload);

        $response->assertStatus(302);
        $this->assertDatabaseHas('subscriptions', [
            'id' => $sub->id,
            'service_name' => 'GitHub Copilot Enterprise',
            'price' => 300000.00,
        ]);
    }

    public function test_can_toggle_subscription_status()
    {
        $sub = Subscription::first();
        $initialStatus = $sub->is_active;

        $response = $this->patch(route('subscriptions.toggle', $sub));

        $response->assertStatus(302);
        $this->assertDatabaseHas('subscriptions', [
            'id' => $sub->id,
            'is_active' => !$initialStatus,
        ]);
    }

    public function test_can_delete_subscription()
    {
        $sub = Subscription::first();
        $id = $sub->id;

        $response = $this->delete(route('subscriptions.destroy', $sub));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('subscriptions', ['id' => $id]);
    }

    public function test_can_bulk_delete_subscriptions()
    {
        $subs = Subscription::take(3)->get();
        $ids = $subs->pluck('id')->toArray();

        $response = $this->delete(route('subscriptions.bulk-destroy'), [
            'ids' => $ids,
        ]);

        $response->assertStatus(302);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('subscriptions', ['id' => $id]);
        }
    }

    public function test_can_seed_demo_data_via_endpoint()
    {
        Subscription::truncate();
        $this->assertDatabaseCount('subscriptions', 0);

        $response = $this->post(route('demo.seed'));

        $response->assertRedirect(route('subscriptions.index'));
        $this->assertGreaterThan(0, Subscription::count());
    }

    public function test_can_export_subscriptions_to_csv()
    {
        $response = $this->get(route('subscriptions.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment; filename="subtrack-subscriptions-', $response->headers->get('Content-Disposition'));

        // Stream and check content headers
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('Nama Layanan', $content);
        $this->assertStringContainsString('Metode Bayar', $content);
        $this->assertStringContainsString('GitHub Copilot', $content);
    }

    public function test_multi_currency_normalization_calculates_correct_idr_cost()
    {
        \Illuminate\Support\Facades\Cache::forget('currency_rates_to_idr');
        \Illuminate\Support\Facades\Http::fake([
            'https://open.er-api.com/*' => \Illuminate\Support\Facades\Http::response([
                'result' => 'success',
                'rates' => [
                    'IDR' => 16000.0,
                    'EUR' => 0.8, // 16000 / 0.8 = 20000 IDR
                    'SGD' => 1.25,
                    'GBP' => 0.8,
                ],
            ], 200),
        ]);

        $category = Category::first();
        $paymentMethod = PaymentMethod::first();

        // USD Subscription: $10/month -> 10 * 16,000 = Rp 160,000 / month
        $usdSub = Subscription::create([
            'service_name' => 'ChatGPT Plus USD',
            'category_id' => $category->id,
            'payment_method_id' => $paymentMethod->id,
            'price' => 10,
            'currency' => 'USD',
            'billing_cycle' => 'monthly',
            'next_billing_date' => Carbon::today()->addDays(15),
            'is_active' => true,
        ]);

        $this->assertEquals(160000, $usdSub->normalized_monthly_cost);
        $this->assertEquals(1920000, $usdSub->normalized_yearly_cost);
        $this->assertEquals('$10.00', $usdSub->formatted_original_price);

        // EUR Subscription: €120/year -> 120 * 20,000 = Rp 2,400,000 / year -> Rp 200,000 / month
        $eurSub = Subscription::create([
            'service_name' => 'Hetzner Server EUR',
            'category_id' => $category->id,
            'payment_method_id' => $paymentMethod->id,
            'price' => 120,
            'currency' => 'EUR',
            'billing_cycle' => 'yearly',
            'next_billing_date' => Carbon::today()->addDays(30),
            'is_active' => true,
        ]);

        $this->assertEquals(200000, $eurSub->normalized_monthly_cost);
        $this->assertEquals(2400000, $eurSub->normalized_yearly_cost);
        $this->assertEquals('€120.00', $eurSub->formatted_original_price);
    }

    public function test_currency_converter_uses_fallback_when_api_fails()
    {
        \Illuminate\Support\Facades\Cache::forget('currency_rates_to_idr');
        \Illuminate\Support\Facades\Http::fake([
            'https://open.er-api.com/*' => \Illuminate\Support\Facades\Http::response(null, 500),
        ]);

        $rates = \App\Services\CurrencyConverter::getRates();

        $this->assertEquals(16250.0, $rates['USD']);
        $this->assertEquals(17600.0, $rates['EUR']);
        $this->assertEquals(1.0, $rates['IDR']);
    }

    public function test_can_import_subscriptions_from_csv()
    {
        $csvContent = "Nama Layanan,Kategori,Metode Bayar,Nominal,Mata Uang,Siklus Tagihan,Tanggal Tagihan Berikutnya\n";
        $csvContent .= "Figma Professional,Design,BCA Virtual Account,15,USD,monthly," . Carbon::today()->addDays(7)->format('Y-m-d') . "\n";
        $csvContent .= "Cursor Pro,Development Tools,Jenius Debit,20,USD,monthly," . Carbon::today()->addDays(14)->format('Y-m-d') . "\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('subscriptions.csv', $csvContent);

        $response = $this->post(route('subscriptions.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect(route('subscriptions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subscriptions', [
            'service_name' => 'Figma Professional',
            'price' => 15.00,
            'currency' => 'USD',
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'service_name' => 'Cursor Pro',
            'price' => 20.00,
            'currency' => 'USD',
        ]);
    }

    public function test_can_import_subscriptions_with_semicolon_delimiter()
    {
        $csvContent = "Nama Layanan;Kategori;Metode Bayar;Harga Asli;Mata Uang;Siklus Penagihan;Jatuh Tempo\n";
        $csvContent .= "Adobe CC;Design Tools;Mandiri CC;120000;IDR;monthly;2026-10-15\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('subscriptions_semicolon.csv', $csvContent);

        $response = $this->post(route('subscriptions.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect(route('subscriptions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subscriptions', [
            'service_name' => 'Adobe CC',
            'price' => 120000.00,
            'currency' => 'IDR',
        ]);
    }

    public function test_import_csv_fails_with_invalid_file_type()
    {
        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->post(route('subscriptions.import'), [
            'csv_file' => $file,
        ]);

        $response->assertSessionHasErrors('csv_file');
    }

    public function test_dashboard_computes_payment_method_breakdown()
    {
        $response = $this->get(route('subscriptions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('paymentBreakdown');
        $this->assertNotNull($response->viewData('paymentBreakdown'));
    }
}
