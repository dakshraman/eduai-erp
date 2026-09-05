<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Admin\StudentInfo\SmStudentReportController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Examination\ProgressCardReportRequest;
use App\Http\Requests\Admin\Examination\TabulationSheetReportRequest;
use App\Models\ApiBaseMethod;
use App\Models\CustomResultSetting;
use App\Models\Shift;
use App\Models\SmAcademicYear;
use App\Models\SmAssignSubject;
use App\Models\SmClass;
use App\Models\SmClassOptionalSubject;
use App\Models\SmExam;
use App\Models\SmExamSetting;
use App\Models\SmExamSetup;
use App\Models\SmExamType;
use App\Models\SmMarksGrade;
use App\Models\SmMarkStore;
use App\Models\SmOptionalSubjectAssign;
use App\Models\SmResultStore;
use App\Models\SmSection;
use App\Models\SmStudent;
use App\Models\StudentRecord;
use App\Support\YearCheck;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\University\Http\Controllers\ExamCommonController;

class SmReportController extends Controller
{
    public function tabulationSheetReport(Request $request)
    {
        /*
        try {
        */
        $branch_id = $this->selectedBranchId($request);
        $exam_types = $this->tabulationExamTypes(getAcademicId(), Auth::user()->school_id, $branch_id);
        $classes = $this->tabulationClasses(getAcademicId(), Auth::user()->school_id, $branch_id);

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['exam_types'] = $exam_types->toArray();
            $data['classes'] = $classes->toArray();

            return ApiBaseMethod::sendResponse($data, null);
        }

        return view('backEnd.reports.tabulation_sheet_report', compact('exam_types', 'classes', 'branch_id'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function tabulationSheetReportSearch(TabulationSheetReportRequest $request)
    {

        /*
        try {
        */
        $user = Auth::user();
        $academic_id = getAcademicId();
        $branch_id = $this->selectedBranchId($request);

        if (moduleStatusCheck('University')) {
            $common = new ExamCommonController();

            return $common->tabulationReportSearch($request);
        }
        if (! $request->student) {
            $allClass = 0;
            $exam_term_id = $request->exam;
            $class_id = $request->class;
            $section_id = $request->section;
            $shift_id = shiftEnable() && ! empty($request->shift) ? $request->shift : '';
            $exam_content = SmExamSetting::where('exam_type', $exam_term_id)
                ->where('active_status', 1)
                ->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->first();

            $exam_types = $this->tabulationExamTypes($academic_id, $user->school_id, $branch_id, ['id', 'title']);
            $classes = $this->tabulationClasses($academic_id, $user->school_id, $branch_id, ['id', 'class_name']);

            $query = SmMarkStore::where([
                ['exam_term_id', $exam_term_id],
                ['class_id', $class_id],
                ['section_id', $section_id],
            ]);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $shift_id);
            }

            $marks = $query->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->get();

            $grade_chart = SmMarksGrade::select('grade_name', 'gpa', 'percent_from as start', 'percent_upto as end', 'description')
                ->where('active_status', 1)
                ->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->orderBy('gpa', 'desc')
                ->get()
                ->toArray();
            $single_exam_term = SmExamType::withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)->find($request->exam);
            $className = SmClass::withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)->find($request->class);
            $sectionName = SmSection::withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)->find($request->section);
            if (shiftEnable() && $request->shift) {
                $shift = Shift::find($shift_id);
                $tabulation_details['shift'] = $shift->shift_name;
            } else {
                $shift = '';
                $tabulation_details['shift'] = '';
            }

            $tabulation_details['exam_term'] = $single_exam_term->title;
            $tabulation_details['class'] = $className->class_name;
            $tabulation_details['section'] = $sectionName->section_name;
            $tabulation_details['grade_chart'] = $grade_chart;
            $year = YearCheck::getYear();

            $query = SmExam::where([
                ['exam_type_id', $exam_term_id],
                ['section_id', $section_id],
                ['class_id', $class_id],
            ]);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $shift_id);
            }

            $examSubjects = $query->where('school_id', $user->school_id)
                ->where('academic_id', $academic_id)
                ->get();

            $examSubjectIds = [];
            foreach ($examSubjects as $examSubject) {
                $examSubjectIds[] = $examSubject->subject_id;
            }

            $query = SmAssignSubject::where([
                ['class_id', $request->class],
                ['section_id', $request->section],
            ]);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $shift_id);
            }

            $subjects = $query->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->whereIn('subject_id', $examSubjectIds)
                ->get();
            $optional_subject_setup = SmClassOptionalSubject::where('class_id', '=', $request->class)->first();
            $student_ids = SmStudentReportController::classSectionStudent($request);
            $students = SmStudent::whereIn('id', $student_ids)
                ->where('school_id', $user->school_id)
                ->get()->sortBy('roll_no');

            $max_grade = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->max('gpa');

            $fail_grade = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', $academic_id)
                ->where('school_id', Auth::user()->school_id)
                ->min('gpa');

            $fail_grade_name = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->where('gpa', $fail_grade)
                ->first();

            return view('backEnd.reports.tabulation_sheet_report', compact('allClass',
                'exam_types',
                'classes',
                'marks',
                'tabulation_details',
                'year',
                'subjects',
                'optional_subject_setup',
                'exam_term_id',
                'class_id',
                'section_id',
                'shift_id',
                'shift',
                'students',
                'max_grade',
                'fail_grade_name',
                'branch_id',
                'exam_content'
            ));

        }
        $exam_term_id = $request->exam;
        $class_id = $request->class;
        $section_id = $request->section;
        $shift_id = shiftEnable() ? $request->shift : '';
        $student_id = $request->student;
        $exam_content = SmExamSetting::where('exam_type', $exam_term_id)
            ->where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->first();

        $optional_subject_setup = SmClassOptionalSubject::where('class_id', '=', $request->class)->first();

        $fail_grade = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->min('gpa');

        $fail_grade_name = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->where('gpa', $fail_grade)
            ->first();

        $max_grade = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->max('gpa');

        $query = SmExam::where([
            ['exam_type_id', $exam_term_id],
            ['section_id', $section_id],
            ['class_id', $class_id],
        ]);

        if (shiftEnable() && $request->shift) {
            $query->where('shift_id', $shift_id);
        }

        $examSubjects = $query->where('school_id', $user->school_id)
            ->where('academic_id', $academic_id)
            ->get();

        $examSubjectIds = [];
        foreach ($examSubjects as $examSubject) {
            $examSubjectIds[] = $examSubject->subject_id;
        }

        $query = StudentRecord::where('student_id', $request->student)
            ->where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->where('is_promote', 0)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id);

        if (shiftEnable() && $request->shift) {
            $query->where('shift_id', $shift_id);
        }

        $student_detail = $query->first();

        $subjects = $student_detail->class->subjects->whereIn('subject_id', $examSubjectIds)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id);

        $year = YearCheck::getYear();

        $optional_subject_mark = '';

        $get_optional_subject = SmOptionalSubjectAssign::where('student_id', '=', $student_detail->student_id)
            ->where('session_id', '=', $student_detail->session_id)
            ->first();

        if ($get_optional_subject) {
            $optional_subject_mark = $get_optional_subject->subject_id;
        }

        $query = SmResultStore::where([
            ['class_id', $request->class],
            ['exam_type_id', $request->exam],
            ['section_id', $request->section],
            ['student_id', $request->student],
        ]);

        if (shiftEnable() && $request->shift) {
            $query->where('shift_id', $shift_id);
        }

        $mark_sheet = $query->whereIn('subject_id', $subjects->pluck('subject_id')->toArray())
            ->where('school_id', $user->school_id)
            ->get();

        if ($request->student== '') {
            $eligible_subjects = SmAssignSubject::where('class_id', $class_id)->where('section_id', $section_id)->where('academic_id', $academic_id)->where('school_id', $user->school_id)->get();
            $eligible_students = SmStudent::where('class_id', $class_id)->where('section_id', $section_id)->where('academic_id', $academic_id)->where('school_id', $user->school_id)->get();
            foreach ($eligible_students as $SingleStudent) {
                foreach ($eligible_subjects as $subject) {
                    $query = SmResultStore::where([
                        ['exam_type_id', $exam_term_id],
                        ['class_id', $class_id],
                        ['section_id', $section_id],
                        ['student_id', $SingleStudent->id],
                        ['subject_id', $subject->subject_id],
                    ]);

                    if (shiftEnable() && $request->shift) {
                        $query->where('shift_id', $shift_id);
                    }

                    $getMark = $query->first();

                    if ($getMark== '') {
                        Toastr::error('Please register marks for all students.!', 'Failed');

                        return redirect()->back();
                        // return redirect()->back()->with('message-danger', 'Please register marks for all students.!');
                    }
                }
            }
        } else {
            $query = SmAssignSubject::where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $shift_id);
            }

            $eligible_subjects = $query->get();

            foreach ($eligible_subjects as $subject) {

                $query = SmResultStore::where([
                    ['exam_type_id', $exam_term_id],
                    ['class_id', $class_id],
                    ['section_id', $section_id],
                    ['student_id', $request->student],
                ]);

                if (shiftEnable() && $request->shift) {
                    $query->where('shift_id', $shift_id);
                }

                $getMark = $query->first();

                if ($getMark== '') {
                    Toastr::error('Please register marks for all students.!', 'Failed');

                    return redirect()->back();
                    // return redirect()->back()->with('message-danger', 'Please register marks for all students.!');
                }
            }
        }

        if ($request->student ) {
            $query = SmMarkStore::where([
                ['exam_term_id', $request->exam],
                ['class_id', $request->class],
                ['section_id', $request->section],
                ['student_id', $request->student],
            ]);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $request->shift);
            }

            $marks = $query->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->get();

            $students = SmStudent::where('id', $request->student)
                ->where('school_id', $user->school_id)
                ->get();

            $query = SmAssignSubject::where([
                ['class_id', $request->class],
                ['section_id', $request->section],
            ])->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $request->shift);
            }

            $subjects = $query->whereIn('subject_id', $examSubjectIds)
                ->get();

            $subject_list_name = [];
            foreach ($subjects as $sub) {
                $subject_list_name[] = $sub->subject->subject_name;
            }

            $grade_chart = SmMarksGrade::select('grade_name', 'gpa', 'percent_from as start', 'percent_upto as end', 'description')
                ->where('active_status', 1)
                ->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->orderBy('gpa', 'desc')
                ->get()
                ->toArray();

            $query = StudentRecord::where('student_id', $request->student)
                ->where('class_id', $request->class)
                ->where('section_id', $request->section)
                ->where('academic_id', $academic_id)
                ->where('is_promote', 0)
                ->where('school_id', $user->school_id);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $request->shift);
            }

            $single_student = $query->first();
            $single_exam_term = SmExamType::withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)->find($request->exam);
            if (shiftEnable() && $request->shift) {
                $shift = Shift::find($shift_id);
                $tabulation_details['shift'] = $shift->shift_name;
            } else {
                $shift = '';
                $tabulation_details['shift'] = '';
            }

            $tabulation_details['student_name'] = $single_student->studentDetail->full_name;
            $tabulation_details['student_roll'] = $single_student->roll_no;
            $tabulation_details['student_admission_no'] = $single_student->studentDetail->admission_no;
            $tabulation_details['student_class'] = $single_student->Class->class_name;
            $tabulation_details['student_section'] = $single_student->section->section_name;
            $tabulation_details['exam_term'] = $single_exam_term->title;
            $tabulation_details['subject_list'] = $subject_list_name;
            $tabulation_details['grade_chart'] = $grade_chart;
            $tabulation_details['record_id'] = $single_student->id;
        } else {
            $query = SmMarkStore::where([
                ['exam_term_id', $request->exam],
                ['class_id', $request->class],
                ['section_id', $request->section],
            ])->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $request->shift);
            }

            $marks = $query->get();
            $students = SmStudent::where('id', $request->student)
                ->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->get();
        }

        $exam_types = $this->tabulationExamTypes($academic_id, $user->school_id, $branch_id);
        $classes = $this->tabulationClasses($academic_id, $user->school_id, $branch_id);

        $single_class = SmClass::withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)->find($request->class);
        $single_section = SmSection::withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)->find($request->section);
        $query = SmAssignSubject::where([
            ['class_id', $request->class],
            ['section_id', $request->section],
        ])
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id);

        if (shiftEnable() && $request->shift) {
            $query->where('shift_id', $request->shift);
        }

        $subjects = $query->whereIn('subject_id', $examSubjectIds)->get();

        foreach ($subjects as $sub) {
            $subject_list_name[] = $sub->subject->subject_name;
        }
        $grade_chart = SmMarksGrade::select('grade_name', 'gpa', 'percent_from as start', 'percent_upto as end', 'description')
            ->where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->orderBy('gpa', 'desc')
            ->get()
            ->toArray();

        $single_exam_term = SmExamType::withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)->find($request->exam);
        if (shiftEnable() && $request->shift) {
            $shift = Shift::find($shift_id);
            $tabulation_details['shift'] = $shift->shift_name;
        } else {
            $shift = '';
            $tabulation_details['shift'] = '';
        }

        $tabulation_details['student_class'] = $single_class->class_name;
        $tabulation_details['student_section'] = $single_section->section_name;
        $tabulation_details['exam_term'] = $single_exam_term->title;
        $tabulation_details['subject_list'] = $subject_list_name;
        $tabulation_details['grade_chart'] = $grade_chart;

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['exam_types'] = $exam_types->toArray();
            $data['classes'] = $classes->toArray();
            $data['marks'] = $marks->toArray();
            $data['subjects'] = $subjects->toArray();
            $data['exam_term_id'] = $exam_term_id;
            $data['class_id'] = $class_id;
            $data['section_id'] = $section_id;
            $data['students'] = $students->toArray();

            return ApiBaseMethod::sendResponse($data, null);
        }
        $get_class = SmClass::where('active_status', 1)
            ->where('id', $request->class)
            ->first();

        $get_section = SmSection::where('active_status', 1)
            ->where('id', $request->section)
            ->first();
        $single = 0;

        $class_name = $get_class->class_name;
        $section_name = $get_section->section_name;

        return view('backEnd.reports.tabulation_sheet_report',
            compact('optional_subject_setup',
                'exam_types',
                'classes',
                'marks',
                'subjects',
                'exam_term_id',
                'class_id',
                'section_id',
                'shift_id',
                'class_name',
                'section_name',
                'students',
                'student_id',
                'tabulation_details',
                'max_grade',
                'optional_subject_mark',
                'mark_sheet',
                'fail_grade_name',
                'year',
                'single',
                'branch_id',
                'exam_content'
            )
        );

        /*
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    // tabulationSheetReportPrint
    public function tabulationSheetReportPrint(Request $request)
    {

        /*
        try {
        */
        $user = Auth::user();
        $academic_id = getAcademicId();
        $branch_id = $this->selectedBranchId($request);

        if (moduleStatusCheck('University')) {
            $common = new ExamCommonController();

            return $common->tabulationReportSearch($request);
        }
        if (! $request->student) {
            $allClass = 0;
            $exam_term_id = $request->exam;
            $class_id = $request->class;
            $section_id = $request->section;
            $shift_id = shiftEnable() ? $request->shift : '';
            $exam_content = SmExamSetting::where('exam_type', $exam_term_id)
                ->where('active_status', 1)
                ->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->first();

            $exam_types = $this->tabulationExamTypes($academic_id, $user->school_id, $branch_id, ['id', 'title']);
            $classes = $this->tabulationClasses($academic_id, $user->school_id, $branch_id, ['id', 'class_name']);

            $query = SmMarkStore::where([
                ['exam_term_id', $exam_term_id],
                ['class_id', $class_id],
                ['section_id', $section_id],
            ]);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $shift_id);
            }

            $marks = $query->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->get();

            $grade_chart = SmMarksGrade::select('grade_name', 'gpa', 'percent_from as start', 'percent_upto as end', 'description')
                ->where('active_status', 1)
                ->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->orderBy('gpa', 'desc')
                ->get()
                ->toArray();
            $single_exam_term = SmExamType::withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)->find($request->exam);
            $className = SmClass::withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)->find($request->class);
            $sectionName = SmSection::withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)->find($request->section);
            if (shiftEnable() && $request->shift) {
                $shift = Shift::find($shift_id);
                $tabulation_details['shift'] = $shift->shift_name;
            } else {
                $shift = '';
                $tabulation_details['shift'] = '';
            }

            $tabulation_details['exam_term'] = $single_exam_term->title;
            $tabulation_details['class'] = $className->class_name;
            $tabulation_details['section'] = $sectionName->section_name;
            $tabulation_details['grade_chart'] = $grade_chart;
            $year = YearCheck::getYear();

            $query = SmExam::where([
                ['exam_type_id', $exam_term_id],
                ['section_id', $section_id],
                ['class_id', $class_id],
            ]);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $shift_id);
            }

            $examSubjects = $query->where('school_id', $user->school_id)
                ->where('academic_id', $academic_id)
                ->get();

            $examSubjectIds = [];
            foreach ($examSubjects as $examSubject) {
                $examSubjectIds[] = $examSubject->subject_id;
            }

            $query = SmAssignSubject::where([
                ['class_id', $request->class],
                ['section_id', $request->section],
            ]);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $shift_id);
            }

            $subjects = $query->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->whereIn('subject_id', $examSubjectIds)
                ->get();
            $optional_subject_setup = SmClassOptionalSubject::where('class_id', '=', $request->class)->first();
            $student_ids = SmStudentReportController::classSectionStudent($request);
            $students = SmStudent::whereIn('id', $student_ids)
                ->where('school_id', $user->school_id)
                ->get()->sortBy('roll_no');

            $max_grade = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->max('gpa');

            $fail_grade = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', $academic_id)
                ->where('school_id', Auth::user()->school_id)
                ->min('gpa');

            $fail_grade_name = SmMarksGrade::where('active_status', 1)
                ->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->where('gpa', $fail_grade)
                ->first();

            return view('backEnd.reports.tabulation_sheet_report_print', compact('allClass',
                'exam_types',
                'classes',
                'marks',
                'tabulation_details',
                'year',
                'subjects',
                'optional_subject_setup',
                'exam_term_id',
                'class_id',
                'section_id',
                'shift_id',
                'shift',
                'students',
                'max_grade',
                'fail_grade_name',
                'branch_id',
                'exam_content'
            ));

        }
        $exam_term_id = $request->exam;
        $class_id = $request->class;
        $section_id = $request->section;
        $shift_id = shiftEnable() ? $request->shift : '';
        $student_id = $request->student;
        $exam_content = SmExamSetting::where('exam_type', $exam_term_id)
            ->where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->first();

        $optional_subject_setup = SmClassOptionalSubject::where('class_id', '=', $request->class)->first();

        $fail_grade = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->min('gpa');

        $fail_grade_name = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->where('gpa', $fail_grade)
            ->first();

        $max_grade = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->max('gpa');

        $query = SmExam::where([
            ['exam_type_id', $exam_term_id],
            ['section_id', $section_id],
            ['class_id', $class_id],
        ]);

        if (shiftEnable() && $request->shift) {
            $query->where('shift_id', $shift_id);
        }

        $examSubjects = $query->where('school_id', $user->school_id)
            ->where('academic_id', $academic_id)
            ->get();

        $examSubjectIds = [];
        foreach ($examSubjects as $examSubject) {
            $examSubjectIds[] = $examSubject->subject_id;
        }

        $query = StudentRecord::where('student_id', $request->student)
            ->where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->where('is_promote', 0)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id);

        if (shiftEnable() && $request->shift) {
            $query->where('shift_id', $shift_id);
        }

        $student_detail = $query->first();

        $subjects = $student_detail->class->subjects->whereIn('subject_id', $examSubjectIds)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id);

        $year = YearCheck::getYear();

        $optional_subject_mark = '';

        $get_optional_subject = SmOptionalSubjectAssign::where('student_id', '=', $student_detail->student_id)
            ->where('session_id', '=', $student_detail->session_id)
            ->first();

        if ($get_optional_subject) {
            $optional_subject_mark = $get_optional_subject->subject_id;
        }

        $query = SmResultStore::where([
            ['class_id', $request->class],
            ['exam_type_id', $request->exam],
            ['section_id', $request->section],
            ['student_id', $request->student],
        ]);

        if (shiftEnable() && $request->shift) {
            $query->where('shift_id', $shift_id);
        }

        $mark_sheet = $query->whereIn('subject_id', $subjects->pluck('subject_id')->toArray())
            ->where('school_id', $user->school_id)
            ->get();

        if ($request->student== '') {
            $eligible_subjects = SmAssignSubject::where('class_id', $class_id)->where('section_id', $section_id)->where('academic_id', $academic_id)->where('school_id', $user->school_id)->get();
            $eligible_students = SmStudent::where('class_id', $class_id)->where('section_id', $section_id)->where('academic_id', $academic_id)->where('school_id', $user->school_id)->get();
            foreach ($eligible_students as $SingleStudent) {
                foreach ($eligible_subjects as $subject) {
                    $query = SmResultStore::where([
                        ['exam_type_id', $exam_term_id],
                        ['class_id', $class_id],
                        ['section_id', $section_id],
                        ['student_id', $SingleStudent->id],
                        ['subject_id', $subject->subject_id],
                    ]);

                    if (shiftEnable() && $request->shift) {
                        $query->where('shift_id', $shift_id);
                    }

                    $getMark = $query->first();

                    if ($getMark== '') {
                        Toastr::error('Please register marks for all students.!', 'Failed');

                        return redirect()->back();
                        // return redirect()->back()->with('message-danger', 'Please register marks for all students.!');
                    }
                }
            }
        } else {
            $query = SmAssignSubject::where('class_id', $class_id)
                ->where('section_id', $section_id)
                ->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $shift_id);
            }

            $eligible_subjects = $query->get();

            foreach ($eligible_subjects as $subject) {

                $query = SmResultStore::where([
                    ['exam_type_id', $exam_term_id],
                    ['class_id', $class_id],
                    ['section_id', $section_id],
                    ['student_id', $request->student],
                ]);

                if (shiftEnable() && $request->shift) {
                    $query->where('shift_id', $shift_id);
                }

                $getMark = $query->first();

                if ($getMark== '') {
                    Toastr::error('Please register marks for all students.!', 'Failed');

                    return redirect()->back();
                    // return redirect()->back()->with('message-danger', 'Please register marks for all students.!');
                }
            }
        }

        if ($request->student ) {
            $query = SmMarkStore::where([
                ['exam_term_id', $request->exam],
                ['class_id', $request->class],
                ['section_id', $request->section],
                ['student_id', $request->student],
            ]);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $request->shift);
            }

            $marks = $query->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->get();

            $students = SmStudent::where('id', $request->student)
                ->where('school_id', $user->school_id)
                ->get();

            $query = SmAssignSubject::where([
                ['class_id', $request->class],
                ['section_id', $request->section],
            ])->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $request->shift);
            }

            $subjects = $query->whereIn('subject_id', $examSubjectIds)
                ->get();

            $subject_list_name = [];
            foreach ($subjects as $sub) {
                $subject_list_name[] = $sub->subject->subject_name;
            }

            $grade_chart = SmMarksGrade::select('grade_name', 'gpa', 'percent_from as start', 'percent_upto as end', 'description')
                ->where('active_status', 1)
                ->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->orderBy('gpa', 'desc')
                ->get()
                ->toArray();

            $query = StudentRecord::where('student_id', $request->student)
                ->where('class_id', $request->class)
                ->where('section_id', $request->section)
                ->where('academic_id', $academic_id)
                ->where('is_promote', 0)
                ->where('school_id', $user->school_id);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $request->shift);
            }

            $single_student = $query->first();
            $single_exam_term = SmExamType::withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)->find($request->exam);
            if (shiftEnable() && $request->shift) {
                $shift = Shift::find($shift_id);
                $tabulation_details['shift'] = $shift->shift_name;
            } else {
                $shift = '';
                $tabulation_details['shift'] = '';
            }

            $tabulation_details['student_name'] = $single_student->studentDetail->full_name;
            $tabulation_details['student_roll'] = $single_student->roll_no;
            $tabulation_details['student_admission_no'] = $single_student->studentDetail->admission_no;
            $tabulation_details['student_class'] = $single_student->Class->class_name;
            $tabulation_details['student_section'] = $single_student->section->section_name;
            $tabulation_details['exam_term'] = $single_exam_term->title;
            $tabulation_details['subject_list'] = $subject_list_name;
            $tabulation_details['grade_chart'] = $grade_chart;
            $tabulation_details['record_id'] = $single_student->id;
        } else {
            $query = SmMarkStore::where([
                ['exam_term_id', $request->exam],
                ['class_id', $request->class],
                ['section_id', $request->section],
            ])->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id);

            if (shiftEnable() && $request->shift) {
                $query->where('shift_id', $request->shift);
            }

            $marks = $query->get();
            $students = SmStudent::where('id', $request->student)
                ->where('academic_id', $academic_id)
                ->where('school_id', $user->school_id)
                ->get();
        }

        $exam_types = $this->tabulationExamTypes($academic_id, $user->school_id, $branch_id);
        $classes = $this->tabulationClasses($academic_id, $user->school_id, $branch_id);

        $single_class = SmClass::withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)->find($request->class);
        $single_section = SmSection::withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)->find($request->section);
        $query = SmAssignSubject::where([
            ['class_id', $request->class],
            ['section_id', $request->section],
        ])
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id);

        if (shiftEnable() && $request->shift) {
            $query->where('shift_id', $request->shift);
        }

        $subjects = $query->whereIn('subject_id', $examSubjectIds)->get();

        foreach ($subjects as $sub) {
            $subject_list_name[] = $sub->subject->subject_name;
        }
        $grade_chart = SmMarksGrade::select('grade_name', 'gpa', 'percent_from as start', 'percent_upto as end', 'description')
            ->where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->orderBy('gpa', 'desc')
            ->get()
            ->toArray();

        $single_exam_term = SmExamType::withoutGlobalScope(\App\Scopes\StatusAcademicSchoolScope::class)->find($request->exam);
        if (shiftEnable() && $request->shift) {
            $shift = Shift::find($shift_id);
            $tabulation_details['shift'] = $shift->shift_name;
        } else {
            $shift = '';
            $tabulation_details['shift'] = '';
        }

        $tabulation_details['student_class'] = $single_class->class_name;
        $tabulation_details['student_section'] = $single_section->section_name;
        $tabulation_details['exam_term'] = $single_exam_term->title;
        $tabulation_details['subject_list'] = $subject_list_name;
        $tabulation_details['grade_chart'] = $grade_chart;

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['exam_types'] = $exam_types->toArray();
            $data['classes'] = $classes->toArray();
            $data['marks'] = $marks->toArray();
            $data['subjects'] = $subjects->toArray();
            $data['exam_term_id'] = $exam_term_id;
            $data['class_id'] = $class_id;
            $data['section_id'] = $section_id;
            $data['students'] = $students->toArray();

            return ApiBaseMethod::sendResponse($data, null);
        }
        $get_class = SmClass::where('active_status', 1)
            ->where('id', $request->class)
            ->first();

        $get_section = SmSection::where('active_status', 1)
            ->where('id', $request->section)
            ->first();
        $single = 0;

        $class_name = $get_class->class_name;
        $section_name = $get_section->section_name;

        return view('backEnd.reports.tabulation_sheet_report_print',
            compact('optional_subject_setup',
                'exam_types',
                'classes',
                'marks',
                'subjects',
                'exam_term_id',
                'class_id',
                'section_id',
                'shift_id',
                'class_name',
                'section_name',
                'students',
                'student_id',
                'tabulation_details',
                'max_grade',
                'optional_subject_mark',
                'mark_sheet',
                'fail_grade_name',
                'year',
                'single',
                'branch_id',
                'exam_content'
            )
        );

        /*
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function progressCardReport(Request $request)
    {
        /*
        try {
        */
        $user = Auth::user();
        $academic_id = $this->selectedAcademicId($request);
        $branch_id = $this->selectedBranchId($request);
        $exams = $this->progressExams($academic_id, $user->school_id, $branch_id);
        $classes = $this->tabulationClasses($academic_id, $user->school_id, $branch_id);
        $sessions = $this->activeAcademicSessions($user->school_id);

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['routes'] = $exams->toArray();
            $data['assign_vehicles'] = $classes->toArray();

            return ApiBaseMethod::sendResponse($data, null);
        }

        return view('backEnd.reports.progress_card_report', compact('exams', 'classes', 'branch_id', 'academic_id', 'sessions'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    // student progress report search by Amit
    public function progressCardReportSearch(ProgressCardReportRequest $request)
    {
        /*
        try {
        */
        $user = Auth::user();
        $academic_id = $this->selectedAcademicId($request);
        $branch_id = $this->selectedBranchId($request);

        if (moduleStatusCheck('University')) {
            $common = new ExamCommonController();

            return $common->progressCardReportSearch((object) $request->all());
        }
        $max_gpa = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->max('gpa');

        $maxgpaname = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->where('gpa', $max_gpa)
            ->first();

        $failgpa = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->min('gpa');

        $failgpaname = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->where('gpa', $failgpa)
            ->first();

        $exam_content = SmExamSetting::whereNull('exam_type')
            ->where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->first();
        $exams = SmExam::where('active_status', 1)
            ->where('class_id', $request->class)
            ->where('section_id', $request->section)
            ->when(shiftEnable() && ! empty($request->shift), fn ($q) => $q->where('shift_id', $request->shift))
            ->when(moduleStatusCheck('Branch') && $branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->get();

        $exam_types = SmExamType::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->pluck('id');

        $classes = $this->tabulationClasses($academic_id, $user->school_id, $branch_id);
        $sessions = $this->activeAcademicSessions($user->school_id);

        $fail_grade = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->min('gpa');

        $fail_grade_name = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->where('gpa', $fail_grade)
            ->first();

        $studentDetails = StudentRecord::where('student_id', $request->student)
            ->where('class_id', $request->class)
            ->where('section_id', $request->section)
            ->when(shiftEnable() && ! empty($request->shift), function ($q) use ($request) {
                return $q->where('shift_id', $request->shift);
            })
            ->when(moduleStatusCheck('Branch') && $branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->first();

        if (! $studentDetails) {
            Toastr::error('Ops! Your result is not found! Please check mark register.', 'Failed');

            return redirect($this->progressCardReportUrl($request))->withInput();
        }

        $marks_grade = SmMarksGrade::where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->orderBy('gpa', 'desc')
            ->get();

        $maxGrade = SmMarksGrade::where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->max('gpa');

        $optional_subject_setup = SmClassOptionalSubject::where('class_id', '=', $request->class)
            ->first();

        $student_optional_subject = SmOptionalSubjectAssign::where('student_id', $request->student)
            ->where('session_id', '=', $studentDetails->session_id)
            ->first();

        $exam_setup = SmExamSetup::where([
            ['class_id', $request->class],
            ['section_id', $request->section]])
            ->when(shiftEnable() && ! empty($request->shift), function ($q) use ($request) {
                return $q->where('shift_id', $request->shift);
            })
            ->where('school_id', $user->school_id)
            ->get();

        $class_id = $request->class;
        $section_id = $request->section;
        $shift_id = $request->shift;
        $student_id = $request->student;

        $examSubjects = SmExam::where([['section_id', $section_id], ['class_id', $class_id]])
            ->when(shiftEnable() && ! empty($request->shift), function ($q) use ($request) {
                return $q->where('shift_id', $request->shift);
            })
            ->when(moduleStatusCheck('Branch') && $branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->get();

        $examSubjectIds = [];
        foreach ($examSubjects as $examSubject) {
            $examSubjectIds[] = $examSubject->subject_id;
        }

        $subjects = SmAssignSubject::where([
            ['class_id', $request->class],
            ['section_id', $request->section]])
            ->when(shiftEnable() && ! empty($request->shift), function ($q) use ($request) {
                return $q->where('shift_id', $request->shift);
            })
            ->where('school_id', $user->school_id)
            ->whereIn('subject_id', $examSubjectIds)
            ->get();

        $assinged_exam_types = [];
        foreach ($exams as $exam) {
            $assinged_exam_types[] = $exam->exam_type_id;
        }
        $assinged_exam_types = array_unique($assinged_exam_types);
        sort($assinged_exam_types);

        foreach ($assinged_exam_types as $assinged_exam_type) {
            if ($request->custom_mark_report !== 'custom_mark_report') {
                $is_percentage = CustomResultSetting::where('exam_type_id', $assinged_exam_type)
                    ->where('academic_id', $academic_id)
                    ->where('school_id', $user->school_id)
                    ->first();

                if (is_null($is_percentage)) {
                    Toastr::error('Please Complete Exam Result Settings .', 'Failed');

                    return redirect('custom-result-setting');
                }
            }

            foreach ($subjects as $subject) {
                $is_mark_available = SmResultStore::where([
                    ['class_id', $request->class],
                    ['section_id', $request->section],
                    ['student_id', $request->student],
                    // ['exam_type_id', $assinged_exam_type]]
                ])->when(shiftEnable() && ! empty($request->shift), function ($q) use ($request) {
                    return $q->where('shift_id', $request->shift);
                })->when(moduleStatusCheck('Branch') && $branch_id, function ($query) use ($branch_id) {
                    return branchWiseApplyFilter($query, 'branch_id', $branch_id);
                })
                    ->first();
                if ($is_mark_available== '') {
                    Toastr::error('Ops! Your result is not found! Please check mark register.', 'Failed');

                    return redirect($this->progressCardReportUrl($request))->withInput();

                }
            }
        }
        $is_result_available = SmResultStore::where([
            ['class_id', $request->class],
            ['section_id', $request->section],
            ['student_id', $request->student]])
            ->when(shiftEnable() && ! empty($request->shift), function ($q) use ($request) {
                return $q->where('shift_id', $request->shift);
            })
            ->when(moduleStatusCheck('Branch') && $branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->where('school_id', $user->school_id)
            ->get();

        $custom_mark_report = $request->custom_mark_report ?? null;
        $shift = Shift::find($shift_id);
        if ($is_result_available->count() > 0) {
            if ($request->custom_mark_report === 'custom_mark_report') {
                $view = 'backEnd.reports.custom_percent_progress_card_report';
            } else {

                $view = 'backEnd.reports.progress_card_report';
            }

            return view($view,
                compact(
                    'exams',
                    'optional_subject_setup',
                    'student_optional_subject',
                    'classes', 'studentDetails',
                    'is_result_available',
                    'subjects',
                    'class_id',
                    'section_id',
                    'shift_id',
                    'shift',
                    'student_id',
                    'exam_types',
                    'assinged_exam_types',
                    'marks_grade',
                    'fail_grade_name',
                    'fail_grade',
                    'maxGrade',
                    'custom_mark_report',
                    'exam_content',
                    'failgpaname',
                    'max_gpa',
                    'maxgpaname',
                    'branch_id',
                    'academic_id',
                    'sessions',
                ));

        }
        Toastr::error('Ops! Your result is not found! Please check mark register.', 'Failed');

        return redirect($this->progressCardReportUrl($request))->withInput();

        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function progressCardPrint(Request $request)
    {
        if (! $request->filled('class_id') || ! $request->filled('section_id')) {
            return redirect()->back();
        }

        $academic_id = $request->academic_id ?? getAcademicId();
        $user = Auth::user();
        $branch_id = $this->selectedBranchId($request);
        /*
        try {
        */
        if (moduleStatusCheck('University')) {
            $common = new ExamCommonController();

            return $common->progressCardReportPrint((object) $request->all());
        }
        $max_gpa = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->max('gpa');

        $maxgpaname = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->where('gpa', $max_gpa)
            ->first();

        $failgpa = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->min('gpa');

        $failgpaname = SmMarksGrade::where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->where('gpa', $failgpa)
            ->first();
        $exam_content = SmExamSetting::withOutGlobalScopes()->whereNull('exam_type')
            ->where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->first();

        $exams = SmExam::withOutGlobalScopes()
            ->where('active_status', 1)
            ->where('class_id', $request->class_id)
            ->when(shiftEnable() && ! empty($request->shift), function ($q) use ($request) {
                return $q->where('shift_id', $request->shift);
            })
            ->when(moduleStatusCheck('Branch') && $branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->where('section_id', $request->section_id)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->get();

        $exam_types = SmExamType::withOutGlobalScopes()->where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->get();

        $classes = $this->tabulationClasses($academic_id, $user->school_id, $branch_id);

        $marks_grade = SmMarksGrade::withOutGlobalScopes()->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->orderBy('gpa', 'desc')
            ->get();

        $fail_grade = SmMarksGrade::withOutGlobalScopes()->where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->min('gpa');

        $fail_grade_name = SmMarksGrade::withOutGlobalScopes()->where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->where('gpa', $fail_grade)
            ->first();

        $exam_setup = SmExamSetup::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->when(shiftEnable() && ! empty($request->shift), function ($q) use ($request) {
                return $q->where('shift_id', $request->shift);
            })
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->get();

        $student_id = $request->student_id;
        $class_id = $request->class_id;
        $section_id = $request->section_id;
        $shift_id = $request->shift;
        $shift = Shift::find($shift_id);

        $student_detail = StudentRecord::where('id', $student_id)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->first();

        $examSubjects = SmExam::withOutGlobalScopes()
            ->where('section_id', $section_id)
            ->where('class_id', $class_id)
            ->when(shiftEnable() && ! empty($request->shift), function ($q) use ($request) {
                return $q->where('shift_id', $request->shift);
            })
            ->when(moduleStatusCheck('Branch') && $branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->get();

        $examSubjectIds = [];
        foreach ($examSubjects as $examSubject) {
            $examSubjectIds[] = $examSubject->subject_id;
        }

        $assinged_exam_types = [];
        foreach ($exams as $exam) {
            $assinged_exam_types[] = $exam->exam_type_id;
        }

        $subjects = SmAssignSubject::withOutGlobalScopes()->where([
            ['class_id', $request->class_id],
            ['section_id', $request->section_id]])
            ->where('school_id', $user->school_id)
            ->when(shiftEnable() && ! empty($request->shift), function ($q) use ($request) {
                return $q->where('shift_id', $request->shift);
            })
            ->whereIn('subject_id', $examSubjectIds)
            ->get();

        $assinged_exam_types = array_unique($assinged_exam_types);
        foreach ($assinged_exam_types as $assinged_exam_type) {
            foreach ($subjects as $subject) {
                $is_mark_available = SmResultStore::where([
                    ['class_id', $request->class_id],
                    ['section_id', $request->section_id],
                    ['student_record_id', $student_id],
                    ['subject_id', $subject->subject_id],
                    // ['exam_type_id', $assinged_exam_type]
                ])
                    ->when(shiftEnable() && ! empty($request->shift), function ($q) use ($request) {
                        return $q->where('shift_id', $request->shift);
                    })
                    ->where('academic_id', $academic_id)
                    ->first();
                if ($is_mark_available== '') {
                    Toastr::error('Ops! Your result is not found! Please check mark register.', 'Failed');

                    return redirect($this->progressCardReportUrl($request))->withInput();
                    // return redirect('progress-card-report')->with('message-danger', 'Ops! Your result is not found! Please check mark register.');
                }
            }
        }
        $is_result_available = SmResultStore::where([
            ['class_id', $request->class_id],
            ['section_id', $request->section_id],
            ['student_record_id', $student_id],
        ])
            ->when(shiftEnable() && ! empty($request->shift), function ($q) use ($request) {
                return $q->where('shift_id', $request->shift);
            })
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->get();

        $optional_subject_setup = SmClassOptionalSubject::where('class_id', '=', $request->class_id)->first();

        $student_optional_subject = SmOptionalSubjectAssign::where('student_id', $student_id)->where('academic_id', '=', $student_detail->academic_id)->first();
        if ($request->custom_mark_report === 'custom_mark_report') {
            $view = 'backEnd.reports.custom_percent_progress_card_report_print';
        } else {
            $view = 'backEnd.reports.progress_card_report_print';
        }

        return view($view,
            compact(
                'optional_subject_setup',
                'student_optional_subject',
                'exams',
                'classes',
                'student_detail',
                'is_result_available',
                'subjects',
                'class_id',
                'section_id',
                'shift_id',
                'shift',
                'student_id',
                'exam_types',
                'assinged_exam_types',
                'marks_grade',
                'fail_grade_name',
                'exam_content',
                'failgpaname',
                'max_gpa',
                'maxgpaname',
                'branch_id',
            )
        );

        /*
        }catch
        (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function customProgressCardReport(Request $request)
    {
        /*
        try {
        */
        $user = Auth::user();
        $academic_id = $this->selectedAcademicId($request);
        $branch_id = $this->selectedBranchId($request);
        $exams = $this->progressExams($academic_id, $user->school_id, $branch_id);
        $classes = $this->tabulationClasses($academic_id, $user->school_id, $branch_id, ['id', 'class_name']);
        $sessions = $this->activeAcademicSessions($user->school_id);
        $custom_mark_report = 'custom_mark_report';

        return view('backEnd.reports.progress_card_report', compact('exams', 'classes', 'custom_mark_report', 'branch_id', 'academic_id', 'sessions'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    private function selectedBranchId(Request $request)
    {
        if (! moduleStatusCheck('Branch')) {
            return null;
        }

        return $request->filled('branch_id') ? $request->branch_id : userBranch();
    }

    private function selectedAcademicId(Request $request)
    {
        return $request->filled('academic_year') ? $request->academic_year : getAcademicId();
    }

    private function progressCardReportUrl(Request $request)
    {
        return $request->custom_mark_report === 'custom_mark_report'
            ? route('custom_progress_card_report_percent')
            : url('progress-card-report');
    }

    private function activeAcademicSessions($school_id)
    {
        return SmAcademicYear::where('active_status', 1)
            ->where('school_id', $school_id)
            ->get();
    }

    private function tabulationExamTypes($academic_id, $school_id, $branch_id, array $columns = ['*'])
    {
        return SmExamType::query()
            ->where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $school_id)
            ->when(moduleStatusCheck('Branch') && $branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->get($columns);
    }

    private function tabulationClasses($academic_id, $school_id, $branch_id, array $columns = ['*'])
    {
        return SmClass::query()
            ->where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $school_id)
            ->when(moduleStatusCheck('Branch') && $branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->get($columns);
    }

    private function progressExams($academic_id, $school_id, $branch_id, array $columns = ['*'])
    {
        return SmExam::query()
            ->where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $school_id)
            ->when(moduleStatusCheck('Branch') && $branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->get($columns);
    }
}
