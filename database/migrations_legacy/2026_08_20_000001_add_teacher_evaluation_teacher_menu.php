<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permission = DB::table('permissions')
            ->where('route', 'teacher-panel-evaluation-report')
            ->first();

        if (! $permission) {
            return;
        }

        DB::table('sm_menus')
            ->where('route', 'teacher-evaluation')
            ->where('role_id', 1)
            ->select(['id', 'school_id'])
            ->get()
            ->each(function (object $parent) use ($permission): void {
                $exists = DB::table('sm_menus')
                    ->where('route', 'teacher-panel-evaluation-report')
                    ->where('role_id', 4)
                    ->where('parent_id', $parent->id)
                    ->exists();

                if ($exists) {
                    return;
                }

                DB::table('sm_menus')->insert([
                    'name' => 'My Report',
                    'module' => null,
                    'route' => 'teacher-panel-evaluation-report',
                    'lang_name' => 'teacherEvaluation.my_report',
                    'section_id' => null,
                    'icon' => null,
                    'status' => 1,
                    'is_saas' => 0,
                    'role_id' => 4,
                    'is_alumni' => null,
                    'menu_status' => 1,
                    'permission_section' => 0,
                    'position' => 5,
                    'default_position' => 5,
                    'parent' => $parent->id,
                    'parent_id' => $parent->id,
                    'school_id' => $parent->school_id,
                    'alternate_module' => null,
                    'permission_id' => $permission->id,
                    'ignore' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (function_exists('clearPermissionMenuCache')) {
                    clearPermissionMenuCache((int) $parent->school_id);
                }
            });
    }

    public function down(): void
    {
        DB::table('sm_menus')
            ->where('route', 'teacher-panel-evaluation-report')
            ->where('role_id', 4)
            ->whereIn('parent_id', DB::table('sm_menus')
                ->where('route', 'teacher-evaluation')
                ->where('role_id', 1)
                ->pluck('id'))
            ->delete();
    }
};
