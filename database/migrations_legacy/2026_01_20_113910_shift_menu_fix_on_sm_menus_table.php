<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $shift_routes = [
            'shift.index',
            'shift.setting',
        ];

        $shift_menus = DB::table('sm_menus')->whereIn('route', $shift_routes)->get();
        $general_settings = DB::table('sm_menus')->where('route', 'general_settings')->where('school_id', 1)->first();
        $academics = DB::table('sm_menus')->where('route', 'academics')->where('school_id', 1)->first();
        if (! empty($shift_menus) && ! empty($general_settings) && ! empty($academics)) {
            foreach ($shift_menus as $menu) {
                if ($menu->route === 'shift.setting') {
                    DB::table('sm_menus')->where('id', $menu->id)->update([
                        'parent_id' => $general_settings->id,
                        'parent' => $general_settings->id,
                    ]);
                }

                if ($menu->route === 'shift.index') {
                    DB::table('sm_menus')->where('id', $menu->id)->update([
                        'parent_id' => $academics->id,
                        'parent' => $academics->id,
                    ]);
                }

            }
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
