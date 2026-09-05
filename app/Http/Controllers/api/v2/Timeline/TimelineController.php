<?php

namespace App\Http\Controllers\api\v2\Timeline;

use App\Http\Controllers\Controller;
use App\Models\SmAcademicYear;
use App\Models\SmStudentTimeline;
use Illuminate\Http\Request;

class TimelineController extends Controller
{
    public function stdTimeline(Request $request)
    {
        $data['timelines'] = SmStudentTimeline::select('id', 'date', 'title', 'description', 'file', 'created_at')
            ->where('staff_student_id', $request->student_timeline_id)
            ->where('type', 'stu')
            ->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())
            ->where('school_id', auth()->user()->school_id)
            ->get();
        return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Timeline list',
            ]);
    }
}
