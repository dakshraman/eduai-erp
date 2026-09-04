<?php

namespace Modules\Saas\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Saas\Entities\SaasSettings;
use Modules\Saas\Entities\SmPackagePlan;

class SaasDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Model::unguard();

        $this->seedPackagePlans();
        $this->seedSaasSettings();

        Model::reguard();
    }

    private function seedPackagePlans(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'duration_days' => 30,
                'price' => 29.00,
                'trial_days' => 14,
                'active_status' => true,
                'features' => 'Homework, Attendance, Fees Management',
                'student_quantity' => 100,
                'staff_quantity' => 1,
                'modules' => ['homework', 'attendance', 'fees'],
                'menus' => ['homework', 'attendance', 'fees'],
            ],
            [
                'name' => 'Pro',
                'duration_days' => 30,
                'price' => 79.00,
                'trial_days' => 14,
                'active_status' => true,
                'features' => 'All modules including Exam, Library, Transport, Accounting, HR',
                'student_quantity' => 500,
                'staff_quantity' => 5,
                'modules' => ['homework', 'attendance', 'fees', 'exam', 'library', 'transport', 'accounting', 'human_resource'],
                'menus' => ['homework', 'attendance', 'fees', 'exam', 'library', 'transport', 'accounting', 'human_resource'],
            ],
            [
                'name' => 'School',
                'duration_days' => 30,
                'price' => 199.00,
                'trial_days' => 14,
                'active_status' => true,
                'features' => 'All modules with LMS, Online Exam, and Priority Support',
                'student_quantity' => 2000,
                'staff_quantity' => 20,
                'modules' => ['homework', 'attendance', 'fees', 'exam', 'library', 'transport', 'accounting', 'human_resource', 'lms', 'online_exam'],
                'menus' => ['homework', 'attendance', 'fees', 'exam', 'library', 'transport', 'accounting', 'human_resource', 'lms', 'online_exam'],
            ],
        ];

        foreach ($plans as $plan) {
            SmPackagePlan::create($plan);
        }
    }

    private function seedSaasSettings(): void
    {
        $settings = [
            ['lang_name' => 'Manage Subscription', 'route' => 'manage_subscription', 'saas_status' => true, 'active_status' => true],
            ['lang_name' => 'Trial Enabled', 'route' => 'trial_enabled', 'saas_status' => true, 'active_status' => true],
            ['lang_name' => 'Default Trial Days', 'route' => 'default_trial_days', 'saas_status' => true, 'active_status' => true],
            ['lang_name' => 'Stripe Enabled', 'route' => 'stripe_enabled', 'saas_status' => true, 'active_status' => true],
        ];

        foreach ($settings as $setting) {
            SaasSettings::create($setting);
        }
    }
}
