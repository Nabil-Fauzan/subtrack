<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Categories
        $categoriesData = [
            ['name' => 'Cloud & Hosting', 'color_hex' => '#3B82F6'],
            ['name' => 'Dev & Productivity', 'color_hex' => '#10B981'],
            ['name' => 'AI & Tooling', 'color_hex' => '#8B5CF6'],
            ['name' => 'Media & Entertainment', 'color_hex' => '#F59E0B'],
        ];

        $categories = [];
        foreach ($categoriesData as $data) {
            $categories[$data['name']] = Category::firstOrCreate(
                ['name' => $data['name']],
                ['color_hex' => $data['color_hex']]
            );
        }

        // 2. Seed Payment Methods
        $paymentMethodsData = [
            'Credit Card',
            'GoPay / E-Wallet',
            'Bank Transfer',
        ];

        $paymentMethods = [];
        foreach ($paymentMethodsData as $name) {
            $paymentMethods[$name] = PaymentMethod::firstOrCreate(['name' => $name]);
        }

        // 3. Seed Subscriptions with realistic data and relative renewal dates
        $today = Carbon::today();

        $subscriptions = [
            [
                'service_name' => 'GitHub Copilot',
                'category_id' => $categories['Dev & Productivity']->id,
                'payment_method_id' => $paymentMethods['GoPay / E-Wallet']->id,
                'price' => 155000.00,
                'currency' => 'IDR',
                'billing_cycle' => 'monthly',
                'next_billing_date' => $today->copy()->addDays(3)->toDateString(), // Renewing Soon (<= 7 days)
                'is_active' => true,
            ],
            [
                'service_name' => 'Spotify Family',
                'category_id' => $categories['Media & Entertainment']->id,
                'payment_method_id' => $paymentMethods['GoPay / E-Wallet']->id,
                'price' => 86900.00,
                'currency' => 'IDR',
                'billing_cycle' => 'monthly',
                'next_billing_date' => $today->copy()->addDays(14)->toDateString(),
                'is_active' => true,
            ],
            [
                'service_name' => 'Vercel Pro',
                'category_id' => $categories['Cloud & Hosting']->id,
                'payment_method_id' => $paymentMethods['Credit Card']->id,
                'price' => 325000.00,
                'currency' => 'IDR',
                'billing_cycle' => 'monthly',
                'next_billing_date' => $today->copy()->addDays(5)->toDateString(), // Renewing Soon (<= 7 days)
                'is_active' => true,
            ],
            [
                'service_name' => 'Midjourney Standard',
                'category_id' => $categories['AI & Tooling']->id,
                'payment_method_id' => $paymentMethods['Credit Card']->id,
                'price' => 480000.00,
                'currency' => 'IDR',
                'billing_cycle' => 'monthly',
                'next_billing_date' => $today->copy()->addDays(20)->toDateString(),
                'is_active' => true,
            ],
            [
                'service_name' => 'JetBrains All Products Pack',
                'category_id' => $categories['Dev & Productivity']->id,
                'payment_method_id' => $paymentMethods['Credit Card']->id,
                'price' => 3950000.00,
                'currency' => 'IDR',
                'billing_cycle' => 'yearly',
                'next_billing_date' => $today->copy()->addMonths(5)->toDateString(),
                'is_active' => true,
            ],
            [
                'service_name' => 'Domain zanlio.my.id',
                'category_id' => $categories['Cloud & Hosting']->id,
                'payment_method_id' => $paymentMethods['Bank Transfer']->id,
                'price' => 175000.00,
                'currency' => 'IDR',
                'billing_cycle' => 'yearly',
                'next_billing_date' => $today->copy()->addDays(2)->toDateString(), // Renewing Soon (<= 7 days)
                'is_active' => true,
            ],
            [
                'service_name' => 'ChatGPT Plus',
                'category_id' => $categories['AI & Tooling']->id,
                'payment_method_id' => $paymentMethods['Credit Card']->id,
                'price' => 330000.00,
                'currency' => 'IDR',
                'billing_cycle' => 'monthly',
                'next_billing_date' => $today->copy()->addDays(18)->toDateString(),
                'is_active' => true,
            ],
            [
                'service_name' => 'Netflix Premium 4K',
                'category_id' => $categories['Media & Entertainment']->id,
                'payment_method_id' => $paymentMethods['Credit Card']->id,
                'price' => 186000.00,
                'currency' => 'IDR',
                'billing_cycle' => 'monthly',
                'next_billing_date' => $today->copy()->subDays(2)->toDateString(), // Overdue
                'is_active' => false,
            ],
            [
                'service_name' => 'Figma Professional',
                'category_id' => $categories['Dev & Productivity']->id,
                'payment_method_id' => $paymentMethods['Credit Card']->id,
                'price' => 720000.00,
                'currency' => 'IDR',
                'billing_cycle' => 'quarterly',
                'next_billing_date' => $today->copy()->addMonths(2)->toDateString(),
                'is_active' => false,
            ],
        ];

        foreach ($subscriptions as $sub) {
            Subscription::create($sub);
        }
    }
}
