<?php

namespace App\Http\Controllers\api\v2\Teacher\Subject;

use App\Http\Controllers\Controller;
use App\Http\Resources\v2\Teacher\Subject\SubjectListResource;
use App\Models\SmAcademicYear;
use App\Models\SmAssignSubject;
use App\Models\SmStaff;
use App\Models\SmSubject;
use App\Scopes\ActiveStatusSchoolScope;
use App\Scopes\StatusAcademicSchoolScope;

class SubjectController extends Controller
{
    public function index()
    {
        $staff = SmStaff::withoutGlobalScope(ActiveStatusSchoolScope::class)
            ->where('school_id', auth()->user()->school_id)
            ->where('user_id', auth()->id())
            ->first();

        $subjectIds = $staff
            ? SmAssignSubject::withoutGlobalScope(StatusAcademicSchoolScope::class)
                ->where('school_id', auth()->user()->school_id)
                ->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())
                ->where('teacher_id', $staff->id)
                ->distinct()
                ->pluck('subject_id')
            : collect();

        $subjects = SmSubject::withoutGlobalScope(StatusAcademicSchoolScope::class)
            ->where('school_id', auth()->user()->school_id)
            ->whereIn('id', $subjectIds)
            ->latest('id')
            ->get();
        $anonymousResourceCollection = SubjectListResource::collection($subjects);
        return response()->json([
                'success' => true,
                'data' => $anonymousResourceCollection,
                'message' => 'Subject list',
            ]);
    }
}
