<?php

namespace App\Http\Controllers\api\v2\Syllabus;

use App\Http\Controllers\Controller;
use App\Models\SmTeacherUploadContent;
use App\Models\StudentRecord;
use App\Scopes\GlobalAcademicScope;
use Illuminate\Http\Request;

class SyllabusController extends Controller
{
    public function studentSyllabus(Request $request)
    {
        $record = StudentRecord::where('school_id', auth()->user()->school_id)
            ->where('id', $request->record_id)
            ->firstOrFail();

        $data = SmTeacherUploadContent::withoutGlobalScope(GlobalAcademicScope::class)
            ->where('content_type', 'sy')
            ->whereNull('course_id')
            ->whereNull('chapter_id')
            ->whereNull('lesson_id')
            ->where('school_id', auth()->user()->school_id)
            ->where('academic_id', $record->academic_id)
            ->where(function ($query) use ($record): void {
                $query->where('available_for_all_classes', 1)
                    ->orWhere(function ($targeted) use ($record): void {
                        $targeted->where(function ($class) use ($record): void {
                            $class->where('class', $record->class_id)->orWhereNull('class');
                        })->where(function ($section) use ($record): void {
                            $section->where('section', $record->section_id)->orWhereNull('section');
                        });
                    });
            })
            ->orderBy('id', 'DESC')
            ->get()
            ->map(function ($value): array {
                return [
                    'id' => (int) $value->id,
                    'upload_date' => (string) $value->upload_date,
                    'content_title' => (string) $value->content_title,
                    'description' => (string) $value->description,
                    'upload_file' => $value->upload_file ? assetPath($value->upload_file) : '',
                ];
            });

        return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Syllabus list',
            ]);
    }
}
