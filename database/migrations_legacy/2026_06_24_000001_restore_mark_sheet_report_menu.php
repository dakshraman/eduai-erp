<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['default_menus', 'sm_menus'] as $table) {
            $this->restoreMarkSheetReport($table);
        }
    }

    public function down(): void
    {
        //
    }

    private function restoreMarkSheetReport(string $table): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $parents = $this->examReportParents($table);

        foreach ($parents as $parent) {
            $existing = DB::table($table)
                ->where('route', 'mark_sheet_report_student')
                ->where(function ($query) use ($parent): void {
                    $query->where('parent_id', $parent->id)
                        ->orWhere('parent', $parent->id);
                })
                ->first();

            if ($existing) {
                DB::table($table)
                    ->where('id', $existing->id)
                    ->update($this->menuPositionUpdate($table, $parent->id));

                $this->deleteDuplicateMarkSheetMenus($table, $parent, $existing->id);

                continue;
            }

            $template = DB::table($table)
                ->where('route', 'mark_sheet_report_student')
                ->where('school_id', $parent->school_id)
                ->where('role_id', $parent->role_id)
                ->orderBy('id')
                ->first();

            if (! $template) {
                continue;
            }

            $row = (array) $template;
            unset($row['id']);

            $row = array_merge($row, $this->menuPositionUpdate($table, $parent->id), [
                'name' => 'Mark Sheet Report',
                'route' => 'mark_sheet_report_student',
                'menu_status' => 1,
            ]);

            if (Schema::hasColumn($table, 'created_at')) {
                $row['created_at'] = now();
            }

            if (Schema::hasColumn($table, 'updated_at')) {
                $row['updated_at'] = now();
            }

            $menuId = DB::table($table)->insertGetId($row);

            $this->deleteDuplicateMarkSheetMenus($table, $parent, $menuId);
        }
    }

    private function deleteDuplicateMarkSheetMenus(string $table, object $parent, int $keepId): void
    {
        DB::table($table)
            ->where('route', 'mark_sheet_report_student')
            ->where('school_id', $parent->school_id)
            ->where('role_id', $parent->role_id)
            ->where('id', '!=', $keepId)
            ->delete();
    }

    private function examReportParents(string $table): \Illuminate\Support\Collection
    {
        if (! Schema::hasColumn($table, 'parent_id')) {
            return collect();
        }

        return DB::table($table.' as exam_report')
            ->join($table.' as report_section', 'exam_report.parent_id', '=', 'report_section.id')
            ->where('exam_report.route', 'exam_report')
            ->where('report_section.route', 'report_section')
            ->select('exam_report.*')
            ->get();
    }

    private function menuPositionUpdate(string $table, int $parentId): array
    {
        $updates = [
            'position' => 4,
        ];

        foreach (['parent_id', 'parent'] as $column) {
            if (Schema::hasColumn($table, $column)) {
                $updates[$column] = $parentId;
            }
        }

        if (Schema::hasColumn($table, 'default_position')) {
            $updates['default_position'] = 4;
        }

        if (Schema::hasColumn($table, 'updated_at')) {
            $updates['updated_at'] = now();
        }

        return $updates;
    }
};
