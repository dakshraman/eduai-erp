<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['sm_menus', 'default_menus'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $hasSchoolId = Schema::hasColumn($table, 'school_id');
            $hasRoleId = Schema::hasColumn($table, 'role_id');

            $rows = DB::table($table)
                ->where('route', 'mark_sheet_report_student')
                ->orderBy('id')
                ->get();

            if ($rows->isEmpty()) {
                continue;
            }

            $groups = $rows->groupBy(function ($row) use ($hasSchoolId, $hasRoleId) {
                $school = $hasSchoolId ? ($row->school_id ?? 'null') : 'all';
                $role = $hasRoleId ? ($row->role_id ?? 'null') : 'all';

                return $school.'|'.$role;
            });

            foreach ($groups as $group) {
                $keep = $group->first();
                $removeIds = $group->slice(1)->pluck('id')->all();

                if (! empty($removeIds)) {
                    DB::table($table)->whereIn('id', $removeIds)->delete();
                }

                if ($hasRoleId && $keep->role_id === null) {
                    DB::table($table)->where('id', $keep->id)->update(['role_id' => 1]);
                }
            }
        }
    }

    public function down(): void
    {
        //
    }
};
