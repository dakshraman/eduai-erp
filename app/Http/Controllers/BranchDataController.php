<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\SmClass;
use App\Scopes\StatusAcademicSchoolScope;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchDataController extends Controller
{
    /**
     * Get shifts by branch and academic year
     */
    public function getBranchShifts(Request $request)
    {
        try {
            $academicId = $request->academic_id ?: getAcademicId();
            $query = Shift::where('active_status', 1)
                ->where('school_id', Auth::user()->school_id)
                ->where('academic_id', $academicId);

            if ($request->branch_id) {
                $query = branchWiseApplyFilter($query, 'branch_id', $request->branch_id);
            }

            $shifts = $query->orderBy('name')->get(['name', 'id']);
            return response()->json($shifts);
        } catch (Exception $exception) {
            return response()->json([], 404);
        }
    }

    /**
     * Get classes by branch and academic year
     */
    public function getBranchClasses(Request $request)
    {
        try {
            $academicId = $request->academic_id ?: getAcademicId();
            $query = SmClass::where('active_status', 1)
                ->where('school_id', Auth::user()->school_id)
                ->where('academic_id', $academicId)
                ->withoutGlobalScope(StatusAcademicSchoolScope::class);

            if ($request->branch_id) {
                $query = branchWiseApplyFilter($query, 'branch_id', $request->branch_id);
            }

            $classes = $query->orderBy('class_name')->get(['class_name', 'id']);
            return response()->json($classes);
        } catch (Exception $exception) {
            return response()->json([], 404);
        }
    }
}
