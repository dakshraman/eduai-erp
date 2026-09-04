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
        $general_settings = DB::table('sm_menus')->where('route', 'general_settings')->where('school_id', 1)->first();
        $academics = DB::table('sm_menus')->where('route', 'academics')->where('school_id', 1)->first();
        $menu = [
            [
                'name' => 'Shifts',
                'route' => 'shift.index',
                'module' => '',
                'lang_name' => 'common.shifts',
                'icon' => '',
                'is_alumni' => 1,
                'is_saas' => 1,
                'status' => 1,
                'menu_status' => 1,
                'permission_section' => 0,
                'position' => 10,
                'default_position' => 10,
                'parent_id' => $academics->id ?? null,
                'parent' => $academics->id ?? null,
                'school_id' => 1,
                'permission_id' => null,
                'role_id' => 1,
                'alternate_module' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Shift Setting',
                'route' => 'shift.setting',
                'module' => '',
                'lang_name' => 'common.shift_setting',
                'icon' => '',
                'is_alumni' => 1,
                'is_saas' => 1,
                'status' => 1,
                'menu_status' => 1,
                'permission_section' => 0,
                'position' => 25,
                'default_position' => 25,
                'parent_id' => $general_settings->id ?? null,
                'parent' => $general_settings->id ?? null,
                'school_id' => 1,
                'permission_id' => null,
                'role_id' => 1,
                'alternate_module' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('sm_menus')->insert($menu);

        DB::table('default_menus')->insert($menu);
    }

    public function down(): void
    {
        DB::table('sm_menus')->whereIn('route', ['shifts.index', 'shift.setting'])->delete();
        DB::table('default_menus')->whereIn('route', ['shifts.index', 'shift.setting'])->delete();
    }
};
