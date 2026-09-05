<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('stripe_price_id');
            $table->string('stripe_product_id');
            $table->unsignedInteger('price_cents');
            $table->string('interval')->default('monthly');
            $table->json('features')->nullable();
            $table->unsignedInteger('max_students')->default(0);
            $table->unsignedInteger('max_teachers')->default(0);
            $table->boolean('active_status')->default(true);
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
