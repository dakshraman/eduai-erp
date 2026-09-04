<?php

use App\Models\SmLanguage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $hasArabic = SmLanguage::where('language_universal', 'ar')->first();
        if (! $hasArabic) {
            $store = new SmLanguage();
            $store->language_name = 'Arabic';
            $store->native = 'العربية';
            $store->language_universal = 'ar';
            $store->lang_id = 3;
            $store->created_at = date('Y-m-d h:i:s');
            $store->save();
        }
        DB::table('sm_general_settings')->update(['system_version' => '9.1.3']);
        if (! app()->environment('testing')) {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
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
