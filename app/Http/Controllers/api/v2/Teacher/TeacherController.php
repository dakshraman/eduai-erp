<?php

namespace App\Http\Controllers\api\v2\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\v2\TeachersListResource;
use App\Models\SmAssignSubject;
use App\Models\StudentRecord;
use App\Scopes\StatusAcademicSchoolScope;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function studentTeacher(Request $request)
    {
        $record = StudentRecord::where('school_id', auth()->user()->school_id)->where('id', $request->record_id)->firstOrFail();
        $result = SmAssignSubject::withoutGlobalScope(StatusAcademicSchoolScope::class)
            ->with('teacher', 'subject')
            ->where('school_id', auth()->user()->school_id)
            ->where('class_id', $record->class_id)
            ->where('section_id', $record->section_id)
            ->distinct('teacher_id')->get();

        $anonymousResourceCollection = TeachersListResource::collection($result);

        return response()->json([
                'success' => true,
                'data' => $anonymousResourceCollection,
                'message' => 'Student teacher list',
            ]);
    }
}
