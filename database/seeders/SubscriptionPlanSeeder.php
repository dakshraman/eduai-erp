<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'stripe_price_id' => 'price_starter_monthly',
                'stripe_product_id' => 'prod_starter',
                'price_cents' => 2900,
                'interval' => 'monthly',
                'features' => ['Up to 100 students', 'Basic reporting', 'Email support'],
                'max_students' => 100,
                'max_teachers' => 10,
                'active_status' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'stripe_price_id' => 'price_pro_monthly',
                'stripe_product_id' => 'prod_pro',
                'price_cents' => 7900,
                'interval' => 'monthly',
                'features' => ['Up to 500 students', 'Advanced reporting', 'Priority support', 'API access'],
                'max_students' => 500,
                'max_teachers' => 50,
                'active_status' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'School',
                'slug' => 'school',
                'stripe_price_id' => 'price_school_monthly',
                'stripe_product_id' => 'prod_school',
                'price_cents' => 19900,
                'interval' => 'monthly',
                'features' => ['Unlimited students', 'Full reporting suite', 'Dedicated support', 'Custom integrations', 'White-label options'],
                'max_students' => 0,
                'max_teachers' => 0,
                'active_status' => true,
                'display_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
