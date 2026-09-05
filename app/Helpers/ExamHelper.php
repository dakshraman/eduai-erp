<?php

/**
 * ExamHelper.php — Exam & result calculation helpers.
 *
 * Extracted from Helper.php (P4 DRY cleanup).
 * Registered in composer.json autoload.files — already loaded globally.
 *
 * Contains: grade computation, GPA, term marks, subject averages,
 * merit position, exam schedules, result print status, attendance.
 */

use App\Models\CustomResultSetting;
use App\Models\ExamMeritPosition;
use App\Models\SmAssignSubject;
use App\Models\SmClassOptionalSubject;
use App\Models\SmExam;
use App\Models\SmExamSchedule;
use App\Models\SmExamSignature;
use App\Models\SmExamType;
use App\Models\SmMarksGrade;
use App\Models\SmMarkStore;
use App\Models\SmOptionalSubjectAssign;
use App\Models\SmResultStore;
use App\Models\SmStudent;
use App\Models\SmSubject;
use App\Models\SmSubjectAttendance;
use App\Models\StudentRecord;
use App\Scopes\AcademicSchoolScope;
use App\Scopes\GlobalAcademicScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if (! function_exists('GetFinalResultBySubjectId')) {
    function GetFinalResultBySubjectId($class_id, $section_id, $subject_id, $exam_id, $student_id)
    {

        try {
            return SmResultStore::where([
                ['class_id', $class_id],
                ['section_id', $section_id],
                ['exam_type_id', $exam_id],
                ['student_id', $student_id],
                ['subject_id', $subject_id],
            ])->first();
        } catch (Exception $exception) {
            return [];
        }
    }
}

if (! function_exists('GetResultBySubjectId')) {
    function GetResultBySubjectId($class_id, $section_id, $subject_id, $exam_id, $student_id)
    {

        try {
            return SmMarkStore::where([
                ['class_id', $class_id],
                ['section_id', $section_id],
                ['exam_term_id', $exam_id],
                ['student_id', $student_id],
                ['subject_id', $subject_id],
            ])->get();
        } catch (Exception $exception) {
            return [];
        }
    }
}

if (! function_exists('allExamsSubjectTotalMark')) {
    function allExamsSubjectTotalMark($subject_id)
    {
        try {
            $toal_mark = 0;
            foreach (examTypes() as $exam) {
                $toal_mark += subjectFullMark($exam->id, $subject_id);
            }

            return $toal_mark;
        } catch (Throwable $throwable) {
            return 100;
        }
    }
}

if (! function_exists('allSubjectAverageMark')) {
    function allSubjectAverageMark($record_id, $subject_id)
    {
        try {
            $exam_rules = CustomResultSetting::where('school_id', Auth()->user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get();
            $total_mark = 0;
            $grade = '';
            if (! is_null($exam_rules)) {
                foreach ($exam_rules as $exam_rule) {
                    $mark = SmResultStore::where('student_record_id', $record_id)->where('subject_id', $subject_id)->where('exam_type_id', $exam_rule->exam_type_id)->first();
                    if ($mark) {
                        $full_mark = SmExam::where('exam_type_id', $mark->exam_type_id)->where('subject_id', $subject_id)->where('class_id', $mark->class_id)->where('section_id', $mark->section_id)->first('exam_mark');
                        $total_mark += ((($mark->total_marks * 100) / $full_mark->exam_mark) * ($exam_rule->exam_percentage / 100));
                    }
                }
            }

            $total_mark = number_format($total_mark, 2);

            return [$total_mark];
        } catch (Exception $exception) {
            return [0];
        }
    }

    if (! function_exists('allExamSubjectMark')) {
        function allExamSubjectMark($record_id, $exam_rule_id, $exam_rule = true)
        {
            try {
                $avg_marks = 0;

                if ($exam_rule) {
                    $exam_rule = CustomResultSetting::find($exam_rule_id);
                    if ($exam_rule) {
                        $result = SmResultStore::where('student_record_id', $record_id)
                            ->where('exam_type_id', $exam_rule->exam_type_id)
                            ->where('academic_id', getAcademicId())->get();
                        if ($result->count()) {
                            $total_marks = $result->sum('total_marks');
                            $avg_marks = ($total_marks / count($result)) * ($exam_rule->exam_percentage / 100);
                        }
                    }
                } else {
                    $result = SmResultStore::where('student_record_id', $record_id)
                        ->where('exam_type_id', $exam_rule_id)
                        ->where('academic_id', getAcademicId())->get();
                    if ($result->count()) {
                        $total_marks = $result->sum('total_marks');
                        $avg_marks = $total_marks / count($result);
                    }
                }

                $avg_marks = number_format($avg_marks, 2);

                return [$avg_marks];
            } catch (Exception $exception) {
                return [0];
            }
        }
    }

    if (! function_exists('allExamSubjectMarkAverage')) {
        function allExamSubjectMarkAverage($record_id, $all_subject_ids)
        {
            try {
                $total_avg = 0;
                if (moduleStatusCheck('University')) {
                    $exam_rules = CustomResultSetting::where('un_academic_id', getAcademicId())->where('school_id', Auth()->user()->school_id)
                        ->where('un_academic_id', getAcademicId())
                        ->get();
                } else {
                    $exam_rules = CustomResultSetting::where('academic_id', getAcademicId())->where('school_id', Auth()->user()->school_id)
                        ->where('academic_id', getAcademicId())
                        ->get();
                }

                if (count($exam_rules) > 0) {
                    foreach ($all_subject_ids as $all_subject_id) {
                        foreach ($exam_rules as $exam_rule) {
                            if (moduleStatusCheck('University')) {
                                $mark = SmResultStore::where('student_record_id', $record_id)->where('un_subject_id', $all_subject_id)->where('exam_type_id', $exam_rule->exam_type_id)->first();
                                $full_mark = SmExam::where('exam_type_id', $mark->exam_type_id)->where('un_subject_id', $all_subject_id)->where('class_id', $mark->class_id)->where('un_section_id', $mark->un_section_id)->first('exam_mark');
                            } else {
                                $mark = SmResultStore::where('student_record_id', $record_id)->where('subject_id', $all_subject_id)->where('exam_type_id', $exam_rule->exam_type_id)->first();
                                $full_mark = SmExam::where('exam_type_id', $mark->exam_type_id)->where('subject_id', $all_subject_id)->where('class_id', $mark->class_id)->where('section_id', $mark->section_id)->first('exam_mark');
                            }

                            if ($mark) {
                                $total_avg += ((($mark->total_marks * 100) / $full_mark->exam_mark) * ($exam_rule->exam_percentage / 100));
                            }
                        }
                    }
                } else {
                    foreach ($all_subject_ids as $all_subject_id) {
                        foreach (examTypes() as $exam) {
                            if (moduleStatusCheck('University')) {
                                $mark = SmResultStore::where('student_record_id', $record_id)->where('un_subject_id', $all_subject_id)->where('exam_type_id', $exam->id)->first();
                            } else {
                                $mark = SmResultStore::where('student_record_id', $record_id)->where('subject_id', $all_subject_id)->where('exam_type_id', $exam->id)->first();
                            }

                            if ($mark) {
                                if (moduleStatusCheck('University')) {
                                    $full_mark = SmExam::where('exam_type_id', $mark->id)->where('un_subject_id', $all_subject_id)->where('un_semester_label_id', $mark->un_semester_label_id)->where('un_section_id', $mark->un_section_id)->first('exam_mark');
                                } else {
                                    $full_mark = SmExam::where('exam_type_id', $mark->id)->where('subject_id', $all_subject_id)->where('class_id', $mark->class_id)->where('section_id', $mark->section_id)->first('exam_mark');
                                }

                                $total_avg += $mark->total_marks;
                            }
                        }
                    }
                }

                if (count($all_subject_ids) > 0) {
                    $average = $total_avg > 0 ? $total_avg / count($all_subject_ids) : 0;

                    return number_format($average, 2);
                }

                return number_format(0, 2);

            } catch (Exception $exception) {
                return 0;
            }
        }
    }

    if (! function_exists('avgSubjectPassMark')) {
        function avgSubjectPassMark($all_subject_ids)
        {
            try {
                $pass_mark = 0;
                $subjects = SmSubject::whereIn('id', $all_subject_ids)->get();
                if (count($subjects) > 0) {

                    $pass_mark = $subjects->sum('pass_mark') / count($subjects);
                }

                return number_format($pass_mark, 2);
            } catch (Exception $exception) {
                return 0;
            }
        }
    }
}

if (! function_exists('assignedRoutine')) {

    function assignedRoutine($class_id, $section_id, $exam_id, $subject_id, $exam_period_id)
    {
        try {
            return SmExamSchedule::where('class_id', $class_id)->where('section_id', $section_id)->where('exam_term_id', $exam_id)->where('subject_id', $subject_id)
                ->where('exam_period_id', $exam_period_id)->first();
        } catch (Exception $exception) {
            return [];
        }
    }
}

if (! function_exists('assignedRoutineSubject')) {

    function assignedRoutineSubject($class_id, $section_id, $exam_id, $subject_id)
    {

        try {
            return SmExamSchedule::where('class_id', $class_id)->where('section_id', $section_id)->where('exam_term_id', $exam_id)->where('subject_id', $subject_id)->first();
        } catch (Exception $exception) {
            return [];
        }
    }
}

if (! function_exists('averagePassingMark')) {
    function averagePassingMark($exam_type_id)
    {
        $examType = SmExamType::find($exam_type_id);
        if ($examType && $examType->is_average === 1) {
            return $examType->average_mark;
        }

        return null;
    }
}

if (! function_exists('examReportSignatures')) {
    function examReportSignatures()
    {
        return SmExamSignature::where('active_status', 1)->get(['title', 'signature']);
    }
}

if (! function_exists('examTypes')) {
    function examTypes()
    {
        try {
            return SmExamType::where('school_id', auth()->user()->school_id)
                ->where('academic_id', getAcademicId())
                ->where('active_status', 1)
                ->get();
        } catch (Throwable $throwable) {
            return [];
        }
    }
}

if (! function_exists('getExamResult')) {
    function getExamResult($exam_id, $student)
    {
        $eligible_subjects = SmAssignSubject::where('class_id', $student->class_id)->where('section_id', $student->section_id)->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)->get();

        foreach ($eligible_subjects as $eligible_subject) {

            $getMark = SmResultStore::where([
                ['exam_type_id', $exam_id],
                ['class_id', $student->class_id],
                ['section_id', $student->section_id],
                ['student_id', $student->id],
                ['subject_id', $eligible_subject->subject_id],
            ])->first();

            if ($getMark== '') {
                return false;
            }

            return SmResultStore::where([
                ['exam_type_id', $exam_id],
                ['class_id', $student->class_id],
                ['section_id', $student->section_id],
                ['student_id', $student->id],
            ])->get();
        }

        return null;
    }
}

if (! function_exists('getFinalResult')) {
    function getFinalResult($exam_id, $class_id, $section_id, $student_id, $percentage)
    {
        try {
            $system_setting = SmGeneralSettings::where('school_id', auth()->user()->school_id)->first();
            $system_setting = $system_setting->session_id;
            $custom_result_setup = CustomResultSetting::where('academic_year', $system_setting)->first();

            $assigned_subject = SmAssignSubject::where('class_id', $class_id)->where('section_id', $section_id)->get();

            $all_subjects_gpa = [];
            foreach ($assigned_subject as $subject) {
                $custom_result = new CustomResultSetting;
                $subject_gpa = $custom_result->getSubjectGpa($exam_id, $class_id, $section_id, $student_id, $subject->subject_id);
                $all_subjects_gpa[] = $subject_gpa[$subject->subject_id][1];
            }

            $percentage = $custom_result_setup->$percentage;
            $term_gpa = array_sum($all_subjects_gpa) / $assigned_subject->count();
            $percentage = number_format((float) $percentage, 2, '.', '');

            return ($percentage / 100) * $term_gpa;
        } catch (Exception $exception) {
            return [];
        }
    }
}

if (! function_exists('getGlobalExamBySecClsSub')) {
    function getGlobalExamBySecClsSub($section_id, $class_id, $subject_id)
    {
        $globalExams = SmExam::withoutGlobalScope(AcademicSchoolScope::class)
            ->withoutGlobalScope(GlobalAcademicScope::class)
            ->where('class_id', $class_id)
            ->where('subject_id', $subject_id)
            ->where('section_id', $section_id)
            ->with('GetGlobalExamTitle')
            ->get();
        if ($globalExams) {
            return $globalExams;
        }

        return [];

    }
}

if (! function_exists('getGrade')) {
    function getGrade($grade)
    {
        $mark = SmMarksGrade::where('from', '<=', $grade)->where('up', '>=', $grade)->where('academic_id', getAcademicId())->first();
        if ($mark) {
            return $mark;
        }

        $fail_grade = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->min('gpa');

        return SmMarksGrade::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->where('gpa', $fail_grade)
            ->first();

    }
}

if (! function_exists('getGradeUpdate')) {
    function getGradeUpdate($grade)
    {
        $mark = SmMarksGrade::where('from', '<=', $grade)->where('up', '>=', $grade)->where('academic_id', getAcademicId())->first();
        if ($mark) {
            return $mark;
        }

        $fail_grade = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->min('gpa');

        return SmMarksGrade::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->where('gpa', $fail_grade)
            ->first();

    }
}

if (! function_exists('getMarksOfPart')) {
    function getMarksOfPart($student_id, $subject_id, $class_id, $section_id, $exam_term_id, $shift_id = null)
    {
        try {
            return SmMarkStore::where([
                ['student_id', $student_id],
                ['class_id', $class_id],
                ['subject_id', $subject_id],
                ['section_id', $section_id],
                ['exam_term_id', $exam_term_id],
            ])
                ->when(! empty($shift_id), function ($q) use ($shift_id) {
                    return $q->where('shift_id', $shift_id);
                })
                ->get();
        } catch (Exception $exception) {
            return [];
        }
    }
}

if (! function_exists('getStudentMeritPosition')) {
    function getStudentMeritPosition($class_id, $section_id, $exam_term_id, $record_id)
    {
        try {
            $query = ExamMeritPosition::withOutGlobalScopes()
                ->where('class_id', $class_id)
                ->where('exam_term_id', $exam_term_id)
                ->where('record_id', $record_id);

            if (! is_null($section_id)) {
                $query->where('section_id', $section_id);
            }

            $position = $query->first();

            if ($position) {
                return $position->position;
            }

            return '';

        } catch (Throwable $throwable) {
            return false;
        }
    }
}

if (! function_exists('getSubjectAttendance')) {
    function getSubjectAttendance($record)
    {
        return SmSubjectAttendance::with('student')
            ->whereIn('academic_id', $record->pluck('academic_id'))
            ->whereIn('student_record_id', $record->pluck('id'))
            ->whereIn('school_id', $record->pluck('school_id'))
            ->get();
    }
}

if (! function_exists('getSubjectGpa')) {
    function getSubjectGpa($class_id, $section_id, $exam_id, $student_id, $subject)
    {
        try {
            $subject_marks = [];
            $subject_mark = DB::table('sm_mark_stores')->where('student_id', $student_id)->where('exam_term_id', '=', $exam_id)->first();

            $custom_result = new CustomResultSetting;
            $subject_gpa = $custom_result->getGpa($subject_mark->total_marks);

            $subject_marks[$subject][0] = $subject_mark->total_marks;
            $subject_marks[$subject][1] = $subject_gpa;

            // return $subject_mark->total_marks;
            return $subject_marks;
        } catch (Exception $exception) {
            return [];
        }
    }
}

if (! function_exists('gpaResult')) {
    function gpaResult($gpa)
    {
        $mark = SmMarksGrade::where('gpa', floor($gpa))->first();
        if ($mark) {
            return $mark;
        }

        return null;

    }
}

if (! function_exists('gradeName')) {

    function gradeName($total_gpa, $academic_id = null)
    {
        $school_id = 1;
        if (Auth::check()) {
            $school_id = Auth::user()->school_id;
        } elseif (app()->bound('school')) {
            $school_id = app('school')->id;
        }

        if (! $academic_id) {
            $academic_id = getAcademicId();
        }

        try {
            return SmMarksGrade::where('academic_id', $academic_id)
                ->where('school_id', $school_id)
                ->where('from', '<=', $total_gpa)
                ->where('up', '>=', $total_gpa)
                ->first('grade_name')->grade_name;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (! function_exists('is_optional_subject')) {
    function is_optional_subject($student_id, $subject_id): bool
    {
        try {
            $result = SmOptionalSubjectAssign::where('student_id', $student_id)->where('subject_id', $subject_id)->first();

            return (bool) $result;

        } catch (Exception $exception) {
            return false;
        }
    }
}

if (! function_exists('labelWiseStudentResult')) {
    /**
     * @return mixed[]
     */
    function labelWiseStudentResult($studentRecord, $subject_id, $examTerm = null): array
    {
        // $subejcts = [1,2,3,4,5];
        // $data = [];
        // foreach($subejcts as $subject) {
        //     $data[$subject]= [
        //         'result' => '',
        //         'total_mark' =>''
        //     ];
        // }
        // $assingSubjects = $studentRecord->unStudentSubjects->pluck('un_subject_id')->toArray();
        $marks = SmMarkStore::withOutGlobalScope(AcademicSchoolScope::class)->where('student_record_id', $studentRecord->id)
            ->where('un_semester_label_id', $studentRecord->un_semester_label_id)
            ->where('un_academic_id', $studentRecord->un_academic_id)
            // ->where('un_subject_id', $subject_id)
            ->where('school_id', auth()->user()->school_id);

        $exit = $marks->get();

        $data = [];
        $data['exit'] = $exit;
        $data['passSubject'] = [];
        $data['total_mark'] = null;
        $data['result'] = 'not taken';
        if (count($exit) > 0) {
            $data['result'] = 'fail';
            $settings = CustomResultSetting::where('school_id', $studentRecord->school_id)
                ->where('un_academic_id', $studentRecord->un_academic_id)
                ->whereNotIn('exam_type_id', [0])
                ->get();
            $subjectPassMark = Modules\University\Entities\UnSubject::where('id', $subject_id)
                ->where('school_id', $studentRecord->school_id)
                ->value('pass_mark');

            if (! $subjectPassMark) {
                $data['result'] = 'pass';
                $data['passSubject'] = [$subject_id];

                return $data;
            }

            if ($settings) {
                $total_mark = 0;
                foreach ($settings as $setting) {
                    $mark = $marks->where('exam_term_id', $setting->exam_type_id)->value('total_marks');
                    $total_mark += ($mark * $setting->exam_percentage) / 100;
                }

                if ($total_mark >= $subjectPassMark) {
                    $data['result'] = 'pass';
                    $data['passSubject'] = [$subject_id];
                }

                $data['total_mark'] = $total_mark;
            } else {
                $totalSubjectMark = $marks->count('total_marks');
                if ($totalSubjectMark >= $subjectPassMark) {
                    $data['result'] = 'pass';
                    $data['passSubject'] = [$subject_id];
                }

                $data['total_mark'] = $totalSubjectMark;
            }
        }

        return $data;
    }
}

if (! function_exists('markGpa')) {
    function markGpa($marks)
    {
        $mark = SmMarksGrade::where([
            ['percent_from', '<=', floor($marks)],
            ['percent_upto', '>=', floor($marks)],
        ])
            ->first();
        if ($mark) {
            return $mark;
        }

        $fail_grade = SmMarksGrade::min('gpa');

        return SmMarksGrade::where('gpa', $fail_grade)->first();

    }
}

if (! function_exists('optionalSubjectFullMark')) {
    function optionalSubjectFullMark($type_id, $student_id, $above_gpa, $purpose = null, $academic_id = null)
    {
        if (! $academic_id) {
            $academic_id = getAcademicId();
        }

        try {
            $subject_ids = SmResultStore::where('student_record_id', $student_id)
                ->where('exam_type_id', $type_id)
                ->where('academic_id', $academic_id)
                ->where('school_id', Auth::user()->school_id)
                ->get('subject_id');

            $additional_subject_id = SmOptionalSubjectAssign::whereIn('subject_id', $subject_ids)
                ->where('record_id', $student_id)
                ->where('academic_id', $academic_id)
                ->where('school_id', Auth::user()->school_id)
                ->first('subject_id')->subject_id;

            if ($purpose === 'optional_sub_gpa') {
                return SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('subject_id', $additional_subject_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->sum('total_gpa_point');
            }

            if ($purpose === 'with_optional_sub_gpa') {
                $total_mark = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('subject_id', $additional_subject_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->sum('total_gpa_point');

                $exam_type_id = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('subject_id', $additional_subject_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->count('exam_type_id');

                return ($total_mark - $above_gpa) * $exam_type_id;
            }
        } catch (Exception $exception) {
            return false;
        }

        return null;
    }
}

if (! function_exists('remarks')) {
    function remarks($total_gpa, $academic_id = null)
    {
        $school_id = 1;
        if (Auth::check()) {
            $school_id = Auth::user()->school_id;
        } elseif (app()->bound('school')) {
            $school_id = app('school')->id;
        }

        if (! $academic_id) {
            $academic_id = getAcademicId();
        }

        try {
            return SmMarksGrade::where('academic_id', $academic_id)
                ->where('school_id', $school_id)
                ->where('from', '<=', $total_gpa)
                ->where('up', '>=', $total_gpa)
                ->first('description')->description;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (! function_exists('resultPrintStatus')) {
    function resultPrintStatus($data): bool
    {
        try {
            $printSettings = CustomResultSetting::first();
            if ($data === 'image') {
                return $printSettings->profile_image === $data;
            }
            if ($data === 'header') {
                return $printSettings->header_background === $data;
            }
            if ($data === 'body') {
                return $printSettings->body_background === $data;
            }
            if ($data === 'vertical_boarder') {
                return $printSettings->vertical_boarder === $data;
            }

            return false;

        } catch (Throwable $throwable) {
            return false;
        }
    }
}

if (! function_exists('singleSubjectMark')) {
    function singleSubjectMark($record_id, $subject_id, $exam_id, $exam_rule = null)
    {

        try {
            $mark = 0;
            $full_mark = 100;

            if (moduleStatusCheck('University')) {
                $sm_mark = SmResultStore::where('student_record_id', $record_id)->where('un_subject_id', $subject_id)->where('exam_type_id', $exam_id)->first();
                if ($sm_mark) {
                    $full_mark = SmExam::where('exam_type_id', $exam_id)->where('un_subject_id', $subject_id)->where('un_semester_label_id', $sm_mark->un_semester_label_id)->where('un_section_id', $sm_mark->un_section_id)->first('exam_mark');
                }
            } else {
                $sm_mark = SmResultStore::where('student_record_id', $record_id)
                    ->where('subject_id', $subject_id)
                    ->where('exam_type_id', $exam_id)
                    ->first();
                if ($sm_mark) {
                    $full_mark = SmExam::where('exam_type_id', $exam_id)
                        ->where('subject_id', $subject_id)
                        ->where('class_id', $sm_mark->class_id)
                        ->where('section_id', $sm_mark->section_id)
                        ->first('exam_mark');
                }
            }

            $mark = $exam_rule === null ? ($sm_mark->total_marks * 100) / $full_mark->exam_mark : $sm_mark->total_marks;

            return [round($mark, 2)];
        } catch (Exception $exception) {
            return [0];
        }
    }
}

if (! function_exists('subject100PercentMark')) {
    function subject100PercentMark(): int
    {
        try {
            return 100;
        } catch (Exception $exception) {
            return 0;
        }
    }
}

if (! function_exists('subjectAverageMark')) {
    function subjectAverageMark($record_id, $subject_id)
    {
        try {
            $total_mark = 0;
            $grade = '';
            if (moduleStatusCheck('University')) {
                $result_setting = CustomResultSetting::where('un_academic_id', getAcademicId())->where('school_id', Auth()->user()->school_id)->get();
            } else {
                $result_setting = CustomResultSetting::where('academic_id', getAcademicId())->where('school_id', Auth()->user()->school_id)->get();
            }

            if ($result_setting) {
                foreach ($result_setting as $exam) {
                    $mark = SmResultStore::query();
                    $mark->where('student_record_id', $record_id)->where('exam_type_id', $exam->exam_type_id);
                    if (moduleStatusCheck('University')) {
                        $mark = $mark->where('un_subject_id', $subject_id);
                    } else {
                        $mark = $mark->where('subject_id', $subject_id);
                    }

                    $mark = $mark->first();

                    if ($mark) {
                        $full_mark = SmExam::query();
                        $full_mark->where('exam_type_id', $mark->exam_type_id);
                        if (moduleStatusCheck('University')) {
                            $full_mark = $full_mark->where('un_subject_id', $subject_id)
                                ->where('un_semester_label_id', $mark->un_semester_label_id)
                                ->where('un_section_id', $mark->un_section_id);
                        } else {
                            $full_mark->where('subject_id', $subject_id)
                                ->where('class_id', $mark->class_id)
                                ->where('section_id', $mark->section_id);
                        }

                        $full_mark = $full_mark->first('exam_mark');
                        $total_mark += ((($mark->total_marks * 100) / $full_mark->exam_mark) * ($exam->exam_percentage / 100));
                    }
                }
            } else {
                foreach (examTypes() as $exam) {
                    $mark = SmResultStore::query();
                    $mark->where('student_record_id', $record_id)->where('exam_type_id', $exam->id);
                    if (moduleStatusCheck('University')) {
                        $mark = $mark->where('un_subject_id', $subject_id);
                    } else {
                        $mark = $mark->where('subject_id', $subject_id);
                    }

                    $mark = $mark->first();

                    if ($mark) {
                        $full_mark = SmExam::query();
                        $full_mark->where('exam_type_id', $mark->exam_type_id);
                        if (moduleStatusCheck('University')) {
                            $full_mark = $full_mark->where('un_subject_id', $subject_id)
                                ->where('un_semester_label_id', $mark->un_semester_label_id)
                                ->where('un_section_id', $mark->un_section_id);
                        } else {
                            $full_mark->where('subject_id', $subject_id)
                                ->where('class_id', $mark->class_id)
                                ->where('section_id', $mark->section_id);
                        }

                        $full_mark = $full_mark->first('exam_mark');
                        $total_mark += $mark->total_marks;
                    }
                }
            }

            $total_mark = number_format($total_mark, 2);

            return [$total_mark];
        } catch (Exception $exception) {
            return [0];
        }
    }
}

if (! function_exists('subjectHighestMark')) {
    function subjectHighestMark($exam_id, $subject_id, $class_id, $section_id, $shift_id = null)
    {

        $school_id = 1;
        if (Auth::check()) {
            $school_id = Auth::user()->school_id;
        } elseif (app()->bound('school')) {
            $school_id = app('school')->id;
        }

        try {
            $highest_mark = SmResultStore::where([['class_id', $class_id], ['exam_type_id', $exam_id], ['section_id', $section_id]])
                ->where('subject_id', $subject_id)
                ->where('school_id', $school_id)
                ->where('academic_id', getAcademicId())
                ->when(shiftEnable(), function ($query) use ($shift_id) {
                    $query->where('shift_id', $shift_id);
                })
                ->max('total_marks');

            return round($highest_mark, 2);
        } catch (Throwable $throwable) {
            return false;
        }
    }
}

if (! function_exists('subjectPercentageMark')) {
    function subjectPercentageMark($obtained_mark, $full_nark)
    {
        if (! $full_nark) {
            return 0;
        }

        try {
            return round(($obtained_mark / $full_nark) * 100, 2);
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (! function_exists('subjectPosition')) {
    /**
     * @return float[]
     */
    function subjectPosition($subject_id, $class_id, $custom_result): array
    {

        $students = SmStudent::where('class_id', $class_id)->get();

        $subject_mark_array = [];
        foreach ($students as $student) {
            $subject_marks = 0;

            $first_exam_mark = SmMarkStore::where('student_id', $student->id)->where('class_id', $class_id)->where('subject_id', $subject_id)->where('exam_term_id', $custom_result->exam_term_id1)->sum('total_marks');

            $subject_marks += $first_exam_mark / 100 * $custom_result->percentage1;

            $second_exam_mark = SmMarkStore::where('student_id', $student->id)->where('class_id', $class_id)->where('subject_id', $subject_id)->where('exam_term_id', $custom_result->exam_term_id2)->sum('total_marks');

            $subject_marks += $second_exam_mark / 100 * $custom_result->percentage2;

            $third_exam_mark = SmMarkStore::where('student_id', $student->id)->where('class_id', $class_id)->where('subject_id', $subject_id)->where('exam_term_id', $custom_result->exam_term_id3)->sum('total_marks');

            $subject_marks += $third_exam_mark / 100 * $custom_result->percentage3;

            $subject_mark_array[] = round($subject_marks);
        }

        arsort($subject_mark_array);

        return $subject_mark_array;
    }
}

if (! function_exists('termResult')) {
    function termResult($exam_id, $class_id, $section_id, $student_id, $subject_count)
    {
        try {
            $assigned_subject = SmAssignSubject::where('class_id', $class_id)->where('section_id', $section_id)->get();
            $mark_store = DB::table('sm_mark_stores')->where([['class_id', $class_id], ['section_id', $section_id], ['exam_term_id', $exam_id], ['student_id', $student_id]])->first();
            $subject_marks = [];
            $subject_gpas = [];
            foreach ($assigned_subject as $subject) {
                $subject_mark = DB::table('sm_mark_stores')->where([['class_id', $class_id], ['section_id', $section_id], ['exam_term_id', $exam_id], ['student_id', $student_id], ['subject_id', $subject->subject_id]])->first();
                $custom_result = new CustomResultSetting; // correct

                $subject_gpa = $custom_result->getGpa($subject_mark->total_marks);
                // return $subject_mark;
                $subject_marks[$subject->subject_id][0] = $subject_mark->total_marks;
                $subject_marks[$subject->subject_id][1] = $subject_gpa;
                $subject_gpas[$subject->subject_id] = $subject_gpa;
            }

            $total_gpa = array_sum($subject_gpas);

            return $total_gpa / $subject_count;
        } catch (Exception $exception) {
            return [];
        }
    }
}

if (! function_exists('termWiseAddOptionalMark')) {
    function termWiseAddOptionalMark($type_id, $student_id, $above_gpa, $academic_id = null)
    {
        if (! $academic_id) {
            $academic_id = getAcademicId();
        }

        try {
            $subject_ids = SmResultStore::where('student_record_id', $student_id)
                ->where('exam_type_id', $type_id)
                ->where('academic_id', $academic_id)
                ->where('school_id', Auth::user()->school_id)
                ->get('subject_id');

            $additional_subject_id = SmOptionalSubjectAssign::whereIn('subject_id', $subject_ids)
                ->where('record_id', $student_id)
                ->where('academic_id', $academic_id)
                ->where('school_id', Auth::user()->school_id)
                ->first('subject_id')->subject_id;

            $additional_subject_mark = SmResultStore::where('student_record_id', $student_id)
                ->where('exam_type_id', $type_id)
                ->where('subject_id', $additional_subject_id)
                ->where('academic_id', $academic_id)
                ->where('school_id', Auth::user()->school_id)
                ->sum('total_gpa_point');

            $additional_single_subject_mark = SmResultStore::where('student_record_id', $student_id)
                ->where('exam_type_id', $type_id)
                ->where('subject_id', $additional_subject_id)
                ->where('academic_id', $academic_id)
                ->where('school_id', Auth::user()->school_id)
                ->first('total_gpa_point')->total_gpa_point;

            $additional_mark_reduction = $additional_single_subject_mark - $above_gpa;
            $all_subject_mark = SmResultStore::where('student_record_id', $student_id)
                ->where('exam_type_id', $type_id)
                ->where('subject_id', '!=', $additional_subject_id)
                ->where('academic_id', $academic_id)
                ->where('school_id', Auth::user()->school_id)
                ->sum('total_gpa_point');

            $without_additional_total_subject = SmResultStore::where('student_record_id', $student_id)
                ->where('exam_type_id', $type_id)
                ->where('subject_id', '!=', $additional_subject_id)
                ->where('academic_id', $academic_id)
                ->where('school_id', Auth::user()->school_id)
                ->count('subject_id');

            $with_additional_full_gpa = $all_subject_mark + ($additional_subject_mark - $above_gpa);

            $percentage = CustomResultSetting::where('exam_type_id', $type_id)
                ->where('academic_id', $academic_id)
                ->where('school_id', Auth::user()->school_id)
                ->first('exam_percentage')->exam_percentage;

            return ($with_additional_full_gpa / $without_additional_total_subject) * ($percentage / 100);
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (! function_exists('termWiseFullMark')) {
    function termWiseFullMark($type_ids, $student_id, $academic_id = null)
    {
        if (! $academic_id) {
            $academic_id = getAcademicId();
        }

        try {
            $average_gpa = 0;
            foreach ($type_ids as $type_id) {
                $total_gpa = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->sum('total_gpa_point');

                $total_subject = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->count('subject_id');

                $percentage = CustomResultSetting::where('exam_type_id', $type_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->first('exam_percentage')->exam_percentage;

                if ($total_subject) {
                    $average_gpa += ($total_gpa / $total_subject) * ($percentage / 100);
                }
            }

            return $average_gpa;
        } catch (Exception $exception) {
            return false;
        }
    }
}

if (! function_exists('termWiseGpa')) {
    function termWiseGpa($type_id, $student_id, $with_optional_subject_mark = null, $academic_id = null)
    {
        if (! $academic_id) {
            $academic_id = getAcademicId();
        }

        try {
            $average_gpa = 0;
            if ($with_optional_subject_mark === null) {
                $total_gpa = SmResultStore::select('total_gpa_point')->where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->sum('total_gpa_point');

                $total_subject = SmResultStore::select('subject_id')->where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->count('subject_id');

                $percentage = CustomResultSetting::where('exam_type_id', $type_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->first('exam_percentage')->exam_percentage;

                if ($total_subject) {
                    $average_gpa += ($total_gpa / $total_subject) * ($percentage / 100);
                }

                return $average_gpa;
            }

            if ($with_optional_subject_mark !== null) {

                $percentage = CustomResultSetting::where('exam_type_id', $type_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->first('exam_percentage')->exam_percentage;

                return $average_gpa + $with_optional_subject_mark * ($percentage / 100);
            }
        } catch (Exception $exception) {
            return false;
        }

        return null;
    }
}

if (! function_exists('termWiseTotalMark')) {
    function termWiseTotalMark($type_id, $student_id, $optional_subject = null, $academic_id = null)
    {
        if (! $academic_id) {
            $academic_id = getAcademicId();
        }

        try {
            if ($optional_subject === null) {
                $average_gpa = 0;
                $total_gpa = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->sum('total_gpa_point');

                $total_subject = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->count('subject_id');

                if ($total_subject) {
                    $average_gpa += $total_gpa / $total_subject;
                }

                return $average_gpa;
            }

            if ($optional_subject !== null) {
                $average_gpa = 0;
                $optional_subject_extra_gpa = 0;

                $class_id = StudentRecord::find($student_id)->class_id;
                $optional_subject_above = SmClassOptionalSubject::where('class_id', $class_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->where('academic_id', $academic_id)
                    ->first('gpa_above')->gpa_above;

                $subject_ids = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->get('subject_id');

                $optional_subject_id = SmOptionalSubjectAssign::whereIn('subject_id', $subject_ids)
                    ->where('student_id', $student_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->first('subject_id')->subject_id;

                $without_optional_subject_gpa = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('subject_id', '!=', $optional_subject_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->sum('total_gpa_point');

                $optional_subject_gpa = SmResultStore::where('student_record_id', $student_id)
                    ->where('exam_type_id', $type_id)
                    ->where('subject_id', $optional_subject_id)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->sum('total_gpa_point');

                $maxgpa = SmMarksGrade::withOutGlobalScopes()->where('academic_id', $academic_id)
                    ->where('school_id', Auth::user()->school_id)
                    ->max('gpa');

                if ($optional_subject_gpa > $optional_subject_above) {
                    $optional_subject_extra_gpa = $optional_subject_gpa - $optional_subject_above;
                }

                $with_optional_subject_extra_gpa = $without_optional_subject_gpa + $optional_subject_extra_gpa;

                $final_gpa_with_optional_subject = $with_optional_subject_extra_gpa / (count($subject_ids) - 1);

                if ($maxgpa < $final_gpa_with_optional_subject) {
                    return $maxgpa;
                }

                return $final_gpa_with_optional_subject;

            }
        } catch (Exception $exception) {
            return false;
        }

        return null;
    }
}
