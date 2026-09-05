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

            $menus = DB::table($table)
                ->where('route', 'mark_sheet_report_student')
                ->orderBy('id')
                ->get()
                ->groupBy(fn ($menu) => ($menu->school_id ?? 0).'|'.($menu->role_id ?? 0));

            foreach ($menus as $group) {
                $sample = $group->first();
                $parent = $this->examReportParent($table, $sample);

                if (! $parent) {
                    continue;
                }

                $keep = $group->firstWhere('parent_id', $parent->id)
                    ?? $group->firstWhere('parent', $parent->id)
                    ?? $group->first();

                DB::table($table)
                    ->whereIn('id', $group->pluck('id')->reject(fn ($id) => $id === $keep->id)->all())
                    ->delete();

                $updates = [
                    'position' => 4,
                    'default_position' => 4,
                    'updated_at' => now(),
                ];

                if ($parent) {
                    $updates['parent_id'] = $parent->id;
                    $updates['parent'] = $parent->id;
                }

                DB::table($table)->where('id', $keep->id)->update($updates);
            }
        }
    }

    public function down(): void
    {
        //
    }

    private function examReportParent(string $table, object $menu): ?object
    {
        if (! Schema::hasColumn($table, 'parent_id')) {
            return null;
        }

        return DB::table($table.' as exam_report')
            ->join($table.' as report_section', 'exam_report.parent_id', '=', 'report_section.id')
            ->where('exam_report.route', 'exam_report')
            ->where('report_section.route', 'report_section')
            ->where('exam_report.school_id', $menu->school_id)
            ->where('exam_report.role_id', $menu->role_id)
            ->select('exam_report.*')
            ->orderBy('exam_report.id')
            ->first();
    }
};
