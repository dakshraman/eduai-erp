<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('sm_general_settings', 'student_grid_view')) {
            Schema::table('sm_general_settings', function ($table) {
                $table->integer('student_grid_view')->default(1);
            });
        }

        if (! Schema::hasColumn('sm_general_settings', 'staff_grid_view')) {
            Schema::table('sm_general_settings', function ($table) {
                $table->integer('staff_grid_view')->default(1);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
