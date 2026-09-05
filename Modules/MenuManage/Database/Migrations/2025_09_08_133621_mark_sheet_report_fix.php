<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MarkSheetReportFix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $schools = DB::table('sm_schools')->get();
        foreach ($schools as $school) {
            $route = DB::table('sm_menus')->where('route', 'mark_sheet_report_student')->where('school_id', $school->id)->first();
            $parent_route = DB::table('sm_menus')->where('route', 'exam_report')->where('school_id', $school->id)->first();
            if (! empty($route) && ! empty($parent_route)) {
                DB::table('sm_menus')->where('route', $route->route)->where('school_id', 1)->update([
                    'parent_id' => $parent_route->id,
                    'parent' => $parent_route->id,
                ]);
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
