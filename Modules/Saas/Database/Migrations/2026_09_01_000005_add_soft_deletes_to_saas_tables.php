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
        Schema::table('sm_package_plans', function (Blueprint $table) {
            if (! Schema::hasColumn('sm_package_plans', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('sm_subscription_payments', function (Blueprint $table) {
            if (! Schema::hasColumn('sm_subscription_payments', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sm_package_plans', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('sm_subscription_payments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
