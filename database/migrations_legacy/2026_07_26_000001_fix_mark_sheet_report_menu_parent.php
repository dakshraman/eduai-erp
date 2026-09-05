<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->fixSmMenus();
        $this->fixDefaultMenus();
    }

    private function fixSmMenus(): void
    {
        if (! Schema::hasTable('sm_menus')) {
            return;
        }

        $schools = DB::table('sm_schools')->pluck('id');

        foreach ($schools as $schoolId) {
            $examReports = DB::table('sm_menus')
                ->where('route', 'exam_report')
                ->where('school_id', $schoolId)
                ->where('role_id', 1)
                ->get();

            $processedParentIds = [];

            foreach ($examReports as $examReport) {
                $existing = DB::table('sm_menus')
                    ->where('route', 'mark_sheet_report_student')
                    ->where('school_id', $schoolId)
                    ->where('role_id', 1)
                    ->where('parent_id', $examReport->id)
                    ->first();

                if ($existing) {
                    DB::table('sm_menus')
                        ->where('id', $existing->id)
                        ->update([
                            'parent_id' => $examReport->id,
                            'parent' => $examReport->id,
                            'menu_status' => 1,
                            'position' => 4,
                            'updated_at' => now(),
                        ]);
                    $processedParentIds[] = $existing->id;
                } else {
                    $template = DB::table('sm_menus')
                        ->where('route', 'mark_sheet_report_student')
                        ->where('school_id', $schoolId)
                        ->where('role_id', 1)
                        ->orderBy('id')
                        ->first();

                    if (! $template) {
                        $template = DB::table('sm_menus')
                            ->where('route', 'mark_sheet_report_student')
                            ->where('school_id', 1)
                            ->where('role_id', 1)
                            ->orderBy('id')
                            ->first();
                    }

                    if ($template) {
                        $row = (array) $template;
                        unset($row['id']);
                        $row['school_id'] = $schoolId;
                        $row['parent_id'] = $examReport->id;
                        $row['parent'] = $examReport->id;
                        $row['menu_status'] = 1;
                        $row['position'] = 4;
                        $row['created_at'] = now();
                        $row['updated_at'] = now();
                        $newId = DB::table('sm_menus')->insertGetId($row);
                        $processedParentIds[] = $newId;
                    }
                }
            }

            $orphanedMarkSheets = DB::table('sm_menus')
                ->where('route', 'mark_sheet_report_student')
                ->where('school_id', $schoolId)
                ->where('role_id', 1)
                ->whereNotIn('parent_id', $examReports->pluck('id'))
                ->get();

            foreach ($orphanedMarkSheets as $orphaned) {
                $targetExamReport = $examReports->first();
                if ($targetExamReport) {
                    DB::table('sm_menus')
                        ->where('id', $orphaned->id)
                        ->update([
                            'parent_id' => $targetExamReport->id,
                            'parent' => $targetExamReport->id,
                            'menu_status' => 1,
                            'position' => 4,
                            'updated_at' => now(),
                        ]);
                }
            }
        }
    }

    private function fixDefaultMenus(): void
    {
        foreach (['default_menus'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $examReports = DB::table($table)
                ->where('route', 'exam_report')
                ->where('school_id', 1)
                ->get();

            foreach ($examReports as $examReport) {
                $existing = DB::table($table)
                    ->where('route', 'mark_sheet_report_student')
                    ->where('school_id', 1)
                    ->where('parent_id', $examReport->id)
                    ->first();

                if ($existing) {
                    DB::table($table)
                        ->where('id', $existing->id)
                        ->update([
                            'parent_id' => $examReport->id,
                            'parent' => $examReport->id,
                            'menu_status' => 1,
                            'position' => 4,
                            'updated_at' => now(),
                        ]);
                }
            }

            $orphaned = DB::table($table)
                ->where('route', 'mark_sheet_report_student')
                ->where('school_id', 1)
                ->whereNotIn('parent_id', $examReports->pluck('id'))
                ->first();

            if ($orphaned && $examReports->isNotEmpty()) {
                DB::table($table)
                    ->where('id', $orphaned->id)
                    ->update([
                        'parent_id' => $examReports->first()->id,
                        'parent' => $examReports->first()->id,
                        'menu_status' => 1,
                        'position' => 4,
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
