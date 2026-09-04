<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\SmAcademicYear;
use App\Models\SmClass;
use App\Models\SmClassSection;
use App\Scopes\StatusAcademicSchoolScope;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftModuleDataGetController extends Controller
{
    public function academicYearGetShift(Request $request)
    {
        try {
            $academic_year = SmAcademicYear::select('id')->where('school_id', Auth::user()->school_id)->where('id', $request->id)->first();

            $query = Shift::where('active_status', '=', '1')
                ->where('academic_id', $academic_year->id)
                ->where('school_id', Auth::user()->school_id);
                
            $shifts = branchWise($query)->get(['name', 'id']);

            return response()->json([$shifts]);
        } catch (\Exception $exception) {
            return response()->json('', 404);
        }
    }

    public function shiftGetClass(Request $request)
    {

        try {
            $academicId = $request->academic_id ?: getAcademicId();
            $shift_wise_class_ids = SmClassSection::where('academic_id', $academicId)
                ->where('shift_id', $request->id)
                ->pluck('class_id');
            if ($shift_wise_class_ids->isEmpty()) {
                return response()->json([], 200);
            }
            $classes = SmClass::where('active_status', 1)
                ->whereIn('id', $shift_wise_class_ids)
                ->where('academic_id', $academicId)
                ->where('school_id', Auth::user()->school_id)
                ->withoutGlobalScope(StatusAcademicSchoolScope::class)
                ->get(['class_name', 'id']);

            return response()->json($classes);
        } catch (Exception $e) {
            return response()->json('', 404);
        }
    }
}
