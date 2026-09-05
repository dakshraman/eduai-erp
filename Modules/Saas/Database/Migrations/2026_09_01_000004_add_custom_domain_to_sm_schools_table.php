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
        Schema::table('sm_schools', function (Blueprint $table) {
            if (! Schema::hasColumn('sm_schools', 'custom_domain')) {
                $table->string('custom_domain')->nullable()->after('school_name');
            }
            if (! Schema::hasColumn('sm_schools', 'subdomain')) {
                $table->string('subdomain')->nullable()->after('custom_domain');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sm_schools', function (Blueprint $table) {
            $table->dropColumn(['custom_domain', 'subdomain']);
        });
    }
};
