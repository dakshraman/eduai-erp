<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('sm_subscription_payments')) {
            Schema::create('sm_subscription_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('sm_schools');
                $table->foreignId('package_plan_id')->constrained('sm_package_plans');
                $table->string('payment_type');
                $table->string('payment_method');
                $table->string('transaction_id')->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('currency', 3)->default('USD');
                $table->string('status');
                $table->date('starts_at');
                $table->date('ends_at');
                $table->date('trial_ends_at')->nullable();
                $table->boolean('is_recurring')->default(false);
                $table->string('stripe_subscription_id')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sm_subscription_payments');
    }
};
