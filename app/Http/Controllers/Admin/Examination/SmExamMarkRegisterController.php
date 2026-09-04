<?php

namespace App\Http\Controllers\Admin\Examination;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Examination\AddMarkRequest;
use App\Imports\BulkImport;
use App\Models\Shift;
use App\Models\SmAssignSubject;
use App\Models\SmClass;
use App\Models\SmExam;
use App\Models\SmExamAttendance;
use App\Models\SmExamAttendanceChild;
use App\Models\SmExamSchedule;
use App\Models\SmExamSetup;
use App\Models\SmExamType;
use App\Models\SmMarksGrade;
use App\Models\SmMarkStore;
use App\Models\SmOptionalSubjectAssign;
use App\Models\SmResultStore;
use App\Models\SmSection;
use App\Models\SmStaff;
use App\Models\SmStudent;
use App\Models\SmSubject;
use App\Models\StudentRecord;
use App\Support\YearCheck;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Modules\University\Entities\UnAcademicYear;
use Modules\University\Entities\UnDepartment;
use Modules\University\Entities\UnFaculty;
use Modules\University\Entities\UnSemester;
use Modules\University\Entities\UnSemesterLabel;
use Modules\University\Entities\UnSession;
use Modules\University\Entities\UnSubject;
use Modules\University\Entities\UnSubjectAssignStudent;
use Modules\University\Repositories\Interfaces\UnCommonRepositoryInterface;

class SmExamMarkRegisterController extends Controller
{
    public function index()
    {
        /*
        try {
        */
        $exams = branchWise(SmExamType::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get());
        if (teacherAccess()) {
            $teacher_info = SmStaff::where('user_id', Auth::user()->id)->first();
            $classes = $teacher_info->classes;
        } else {
            $classes = branchWise(SmClass::get());
        }

        $exam_types = branchWise(SmExamType::get());

        return view('backEnd.examination.masks_register', compact('exams', 'classes', 'exam_types'));
        /*
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function create()
    {
        /*
        try{
        */
        $exams = branchWise(SmExamType::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get());

        $exam_types = branchWise(SmExamType::get());
        if (teacherAccess()) {
            $teacher_info = SmStaff::where('user_id', Auth::user()->id)->first();
            $classes = $teacher_info->classes;
        } else {
            $classes = branchWise(SmClass::get());
        }
        $subjects = branchWise(SmSubject::get());

        return view('backEnd.examination.masks_register_create', compact('exams', 'classes', 'subjects', 'exam_types'));
        /*
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function search(AddMarkRequest $request)
    {
        /*
        try {
        */
        if (moduleStatusCheck('University')) {
            $data = [];
            $un_section = null;
            if ($request->un_section_id) {
                $un_section = SmSection::find($request->un_section_id);
            }
            $un_session = UnSession::find($request->un_session_id);
            $un_faculty = UnFaculty::find($request->un_faculty_id);
            $un_department = UnDepartment::find($request->un_department_id);
            $un_academic = UnAcademicYear::find($request->un_academic_id);
            $un_semester = UnSemester::find($request->un_semester_id);
            $un_semester_label = UnSemesterLabel::find($request->un_semester_label_id);
            $un_section = SmSection::find($request->un_section_id);

            $SmExam = SmExam::query();
            $sm_exam = universityFilter($SmExam, $request)
                ->where('exam_type_id', $request->exam_type)
                ->where('un_subject_id', $request->subject_id)
                ->orWhereNull('un_section_id')
                ->first();

            $exam_id = $sm_exam->id;
            $exam_type_id = $request->exam_type;
            $subject_id = $request->subject_id;
            $exam_type = SmExamType::find($request->exam_type);
            $subjectName = UnSubject::find($request->subject_id);

            $assignSubjects = UnSubjectAssignStudent::query();

            $students = unFilterBySub($assignSubjects, $request)
                ->whereHas('studentDetail', function ($q) {
                    $q->where('active_status', 1);
                })->get();

            $exam_schedule = SmExamSchedule::where('exam_id', $sm_exam->id)
                ->where('academic_id', getAcademicId())
                ->first();

            $data['un_semester_label_id'] = $request->un_semester_label_id;
            $interface = App::make(UnCommonRepositoryInterface::class);
            $data = $interface->oldValueSelected($request);
            if ($students->count() < 1) {
                Toastr::error('Student is not found in according this class and section!', 'Failed');

                return redirect()->back();
            }
            $SmExamSetu = SmExamSetup::query();

            $marks_entry_form = $SmExamSetu->where('un_semester_label_id', $request->un_semester_label_id);

            $marks_entry_form->where(
                [
                    ['exam_term_id', $request->exam_type],
                    ['un_subject_id', $request->subject_id],
                    ['un_section_id', $request->un_section_id],
                ]
            );
            $marks_entry_form = $SmExamSetu->get();

            if ($marks_entry_form->count() > 0) {
                $number_of_exam_parts = count($marks_entry_form);

                return view('backEnd.examination.masks_register_create', compact(
                    'students',
                    'number_of_exam_parts',
                    'marks_entry_form',
                    'exam_id',
                    'subject_id',
                    'un_session',
                    'un_faculty',
                    'un_department',
                    'un_academic',
                    'un_semester',
                    'un_semester_label',
                    'un_section',
                    'subjectName',
                    'exam_type',
                    'exam_type_id',
                    'un_section',
                ))->with($data);
            }

            Toastr::error('No result found or exam setup is not done!', 'Failed');

            return redirect()->back();

            // return view('backEnd.examination.masks_register_create', compact('students', 'exam_id', 'subject_id', 'marks_register_subjects', 'assign_subject_ids','un_session','un_faculty','un_department','un_academic','un_semester','un_semester_label','subjectName','exam_type','exam_type_id'))->with($data);
        }
        $exam = SmExam::query();
        $exam->where('exam_type_id', $request->exam)
            ->where('subject_id', $request->subject)
            ->where('class_id', $request->class)
            ->when($request->shift, function ($q) use ($request) {
                $q->where('shift_id', $request->shift);
            });

        if (empty($request->section)) {
            $exam = $exam->first();
            if (! $exam) {
                Toastr::error('Sorry ! Exam setup is not set yet.', 'Failed');

                return redirect()->back();
            }

            $classSections = SmAssignSubject::where('class_id', $request->class)
                ->where('subject_id', $request->subject)
                ->where('school_id', auth()->user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get(['section_id']);

            $exam_attendance = SmExamAttendance::where('class_id', $request->class)
                ->where('exam_id', $exam->id)
                ->where('subject_id', $request->subject)
                ->first();
        } else {
            $exam = $exam->where('section_id', $request->section)->first();
            if (! $exam) {
                Toastr::error('Sorry ! Exam setup is not set yet.', 'Failed');

                return redirect()->back();
            }

            $exam_attendance = SmExamAttendance::where('class_id', $request->class)->where('section_id', $request->section)
                ->when($request->shift, function ($q) use ($request) {
                    $q->where('shift_id', $request->shift);
                })
                ->where('exam_id', $exam->id)->where('subject_id', $request->subject)->first();
        }

        if ($exam_attendance== '' && ! isSkip('exam_attendance')) {
            Toastr::error('Exam Attendance not taken yet, please check exam attendance', 'Failed');

            return redirect()->back();
        }

        $exams = SmExamType::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get();
        $classes = SmClass::get();
        $exam_types = SmExamType::get();
        $exam_id = $request->exam;
        $class_id = $request->class;
        $section_id = $request->section;
        $shift_id = shiftEnable() ? $request->shift : '';
        $subject_id = $request->subject;
        $subjectNames = SmSubject::where('id', $subject_id)->first();

        $exam_type = SmExamType::find($exam->examType->id);
        $class = SmClass::find($request->class);
        $section = SmSection::find($request->section);
        $shift = null;
        $search_info['shift_name'] = '';
        if (shiftEnable() && $request->shift) {
            $shift = Shift::find($request->shift);
            $search_info['shift_name'] = $shift->shift_name;
        }

        $search_info['exam_name'] = $exam->examType->title;
        $search_info['class_name'] = $class->class_name;
        if ($request->section ) {
            $search_info['section_name'] = $section->section_name;
        } else {
            $search_info['section_name'] = 'All Sections';
        }

        $optional_subject_student_id = SmOptionalSubjectAssign::where('subject_id', $request->subject)
            ->pluck('record_id');

        $students = StudentRecord::with('class', 'section')
            ->when($request->academic_year, function ($query) use ($request) {
                $query->where('academic_id', $request->academic_year);
            })
            ->whereHas('studentDetail', function ($q) {
                $q->where('active_status', 1);
            })
            ->when($request->class, function ($query) use ($request) {
                $query->where('class_id', $request->class);
            })
            ->when($request->section, function ($query) use ($request) {
                $query->where('section_id', $request->section);
            })
            ->when($request->shift, function ($query) use ($request) {
                $query->where('shift_id', $request->shift);
            })
            ->when(! $request->academic_year, function ($query) {
                $query->where('academic_id', getAcademicId());
            })->where('school_id', auth()->user()->school_id)->where('is_promote', 0)->whereHas('studentDetail', function ($q) {
                $q->where('active_status', 1);
            })->where('is_promote', 0)
            ->when($optional_subject_student_id->isNotEmpty(), function ($q) use ($optional_subject_student_id) {
                $q->whereIn('id', $optional_subject_student_id);
            })->get()->sortBy('roll_no');

        $exam_schedule = SmExamSchedule::where('exam_id', $request->exam)->where('class_id', $request->class)->where('section_id', $request->section)
            ->when($request->shift, function ($query) use ($request) {
                $query->where('shift_id', $request->shift);
            })
            ->where('academic_id', getAcademicId())->first();

        if ($students->count() < 1) {
            Toastr::error('Student is not found in according this class and section!', 'Failed');

            return redirect()->back();
        }
        if ($request->section ) {
            $marks_entry_form = SmExamSetup::with('class', 'section')->where(
                [
                    ['exam_term_id', $exam->examType->id],
                    ['class_id', $class_id],
                    ['section_id', $section_id],
                    ['subject_id', $subject_id],
                ])
                ->when(shiftEnable(), function ($query) use ($shift_id) {
                    return $query->where('shift_id', $shift_id);
                })
                ->where('academic_id', getAcademicId())->get();
        } else {
            $marks_entry_form = SmExamSetup::with('class', 'section')->where(
                [
                    ['exam_term_id', $exam->examType->id],
                    ['class_id', $class_id],
                    ['subject_id', $subject_id],
                ])
                ->when(shiftEnable(), function ($query) use ($shift_id) {
                    return $query->where('shift_id', $shift_id);
                })
                ->whereIn('section_id', $classSections)->where('academic_id', getAcademicId())->orderby('id', 'ASC')->get();
        }

        if ($marks_entry_form->count() > 0) {
            $number_of_exam_parts = count($marks_entry_form);

            return view('backEnd.examination.masks_register_create', compact('exams', 'classes', 'students', 'exam_id', 'shift_id', 'class_id', 'section_id', 'subject_id', 'subjectNames', 'number_of_exam_parts', 'marks_entry_form', 'exam_types', 'exam_type', 'search_info'));
        }
        Toastr::error('No result found or exam setup is not done!', 'Failed');

        return redirect()->back();

        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if (moduleStatusCheck('University')) {
                $abc = [];
                $exam_id = $request->exam_id;
                $exam_type_id = $request->exam_type_id;
                $subject_id = $request->subject_id;
                $counter = 0;

                foreach ($request->markStore as $record_id => $record) {
                    $sid = gv($record, 'student');
                    $marks = gv($record, 'marks', []);
                    $absent_students = [gv($record, 'absent_students')];
                    $admission_no = gv($record, 'admission_no');
                    $roll_no = gv($record, 'roll_no');

                    if (! empty($marks)) {
                        $exam_setup_count = 0;
                        $total_marks_persubject = 0;
                        foreach ($marks as $part_mark) {
                            $mark_by_exam_part = ($part_mark === null) ? 0 : $part_mark;
                            $total_marks_persubject = $total_marks_persubject + $mark_by_exam_part;
                            $exam_setup_id = gv($record, 'exam_Sids', [])[$exam_setup_count];

                            $SmMarkStore = SmMarkStore::query();
                            $previous_record = universityFilter($SmMarkStore, $request)
                                ->where([
                                    ['un_subject_id', $subject_id],
                                    ['exam_term_id', $exam_type_id],
                                    ['student_record_id', $record_id],
                                    ['exam_setup_id', $exam_setup_id],
                                    ['student_id', $sid],
                                ])
                                ->where('academic_id', getAcademicId())
                                ->first();

                            if ($previous_record== '' || $previous_record === null) {
                                $marks_register = new SmMarkStore();
                                $marks_register->exam_term_id = $exam_type_id;
                                $marks_register->un_subject_id = $subject_id;
                                $marks_register->student_id = $sid;
                                $marks_register->student_record_id = $record_id;
                                $marks_register->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                                $marks_register->total_marks = $mark_by_exam_part;
                                $marks_register->exam_setup_id = $exam_setup_id;
                                $common = App::make(UnCommonRepositoryInterface::class);
                                $common->storeUniversityData($marks_register, $request);

                                if (isset($absent_students)) {
                                    if (in_array($record_id, $absent_students)) {
                                        $marks_register->is_absent = 1;
                                    } else {
                                        $marks_register->is_absent = 0;
                                    }
                                }
                                $marks_register->teacher_remarks = gv($record, 'teacher_remarks');
                                $marks_register->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                                $marks_register->school_id = Auth::user()->school_id;
                                $marks_register->un_academic_id = getAcademicId();

                                $marks_register->save();
                                $marks_register->toArray();

                            } else {
                                $pid = $previous_record->id;
                                $marks_register = SmMarkStore::find($pid);
                                $marks_register->total_marks = $mark_by_exam_part;

                                if (isset($absent_students)) {
                                    if (in_array($record_id, $absent_students)) {
                                        $marks_register->is_absent = 1;
                                    } else {
                                        $marks_register->is_absent = 0;
                                    }
                                }
                                $marks_register->teacher_remarks = gv($record, 'teacher_remarks');
                                $marks_register->save();
                            }
                            $exam_setup_count++;
                        }

                        $subject_full_mark = un_subjectFullMark($request->exam_type_id, $subject_id, $request);
                        $student_obtained_mark = $total_marks_persubject;
                        $mark_by_persentage = subjectPercentageMark($student_obtained_mark, $subject_full_mark);

                        $mark_grade = SmMarksGrade::where([
                            ['percent_from', '<=', $mark_by_persentage],
                            ['percent_upto', '>=', $mark_by_persentage]])
                            ->where('academic_id', getAcademicId())
                            ->where('school_id', Auth::user()->school_id)
                            ->first();
                        $abc[] = $total_marks_persubject;

                        $SmResultStore = SmResultStore::query();
                        $previous_result_record = universityFilter($SmResultStore, $request)
                            ->where([
                                ['un_subject_id', $subject_id],
                                ['exam_type_id', $exam_type_id],
                                ['student_record_id', $record_id],
                                ['student_id', $sid],
                            ])->first();

                        if ($previous_result_record== '' || $previous_result_record === null) {
                            $result_record = new SmResultStore();
                            $result_record->un_subject_id = $subject_id;
                            $result_record->exam_type_id = $exam_type_id;
                            $result_record->student_id = $sid;
                            $result_record->student_record_id = $record_id;

                            $common = App::make(UnCommonRepositoryInterface::class);
                            $common->storeUniversityData($result_record, $request);

                            if (isset($absent_students)) {
                                if (in_array($record_id, $absent_students)) {
                                    $result_record->is_absent = 1;
                                } else {
                                    $result_record->is_absent = 0;
                                }
                            }
                            $result_record->total_marks = $total_marks_persubject;
                            $result_record->total_gpa_point = @$mark_grade->gpa;
                            $result_record->total_gpa_grade = @$mark_grade->grade_name;
                            $result_record->teacher_remarks = gv($record, 'teacher_remarks');
                            $result_record->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                            $result_record->school_id = Auth::user()->school_id;
                            $result_record->un_academic_id = getAcademicId();
                            $result_record->save();
                            $result_record->toArray();

                        } else {
                            $id = $previous_result_record->id;
                            $result_record = SmResultStore::find($id);
                            $result_record->total_marks = $total_marks_persubject;
                            $result_record->total_gpa_point = @$mark_grade->gpa;
                            $result_record->total_gpa_grade = @$mark_grade->grade_name;
                            $result_record->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                            if (isset($absent_students)) {
                                if (in_array($record_id, $absent_students)) {
                                    $result_record->is_absent = 1;
                                } else {
                                    $result_record->is_absent = 0;
                                }
                            }
                            $result_record->teacher_remarks = gv($record, 'teacher_remarks');
                            $result_record->save();
                            $result_record->toArray();
                        }
                    }
                } // end student loop
            } else {
                $abc = [];
                $class_id = $request->class_id;
                if ($request->section_id ) {
                    $section_id = $request->section_id;
                }
                $subject_id = $request->subject_id;
                // $exam_id = SmExam::find($request->exam_id)->exam_type_id;

                $exam = SmExam::query()
                    ->where('exam_type_id', $request->exam_id)
                    ->where('subject_id', $request->subject_id)
                    ->where('class_id', $request->class_id)
                    ->first();
                $exam_id = $exam->exam_type_id;

                $counter = 0;           // Initilize by 0

                foreach ($request->markStore as $record_id => $record) {
                    $sid = gv($record, 'student');
                    $marks = gv($record, 'marks', []);
                    $absent_students = [gv($record, 'absent_students')];

                    if ($request->section_id== '') {
                        $section_id = gv($record, 'section');
                    }
                    if (shiftEnable()) {
                        if ($request->shift_id== '') {
                            $shift_id = gv($record, 'shift');
                        } else {
                            $shift_id = $request->shift_id;
                        }
                    } else {
                        $shift_id = '';
                    }
                    $admission_no = gv($record, 'admission_no');
                    $roll_no = gv($record, 'roll_no');
                    if (! empty($marks)) {
                        $exam_setup_count = 0;
                        $total_marks_persubject = 0;
                        foreach ($marks as $part_mark) {
                            $mark_by_exam_part = ($part_mark === null) ? 0 : $part_mark;
                            // 0=If exam part is empty
                            $total_marks_persubject = $total_marks_persubject + $mark_by_exam_part;
                            // $is_absent = ($request->abs[$sid]==null) ? 0 : 1;
                            $exam_setup_id = gv($record, 'exam_Sids', [])[$exam_setup_count];

                            $delete_old_record = SmMarkStore::where('class_id', $class_id)
                                ->where('section_id', $section_id)
                                ->where('subject_id', $subject_id)
                                ->where('exam_term_id', $exam_id)
                                ->where('student_record_id', $record_id)
                                ->where('exam_setup_id', $exam_setup_id)
                                ->where('student_id', $sid)
                                ->when(shiftEnable(), function ($query) use ($shift_id) {
                                    return $query->where('shift_id', $shift_id);
                                })
                                ->where('academic_id', getAcademicId())->delete();

                            $old_recored = SmMarkStore::where('class_id', $class_id)
                                ->where('section_id', $section_id)
                                ->where('subject_id', $subject_id)
                                ->where('exam_term_id', $exam_id)
                                ->where('student_record_id', $record_id)
                                ->where('exam_setup_id', $exam_setup_id)
                                ->where('student_id', $sid)
                                ->when(shiftEnable(), function ($query) use ($shift_id) {
                                    return $query->where('shift_id', $shift_id);
                                })
                                ->where('academic_id', getAcademicId())->first();
                            // Is previous record exist ?

                            if (empty($old_recored)) {

                                $marks_register = new SmMarkStore();
                                $marks_register->exam_term_id = $exam_id;
                                $marks_register->class_id = $class_id;
                                $marks_register->section_id = $section_id;
                                $marks_register->subject_id = $subject_id;
                                $marks_register->shift_id = $shift_id;
                                $marks_register->student_id = $sid;
                                $marks_register->student_record_id = $record_id;
                                $marks_register->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                                $marks_register->total_marks = $mark_by_exam_part;
                                $marks_register->exam_setup_id = $exam_setup_id;
                                if (isset($absent_students)) {
                                    if (in_array($record_id, $absent_students)) {
                                        $marks_register->is_absent = 1;
                                    } else {
                                        $marks_register->is_absent = 0;
                                    }
                                }
                                $marks_register->teacher_remarks = gv($record, 'teacher_remarks');
                                $marks_register->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                                $marks_register->school_id = Auth::user()->school_id;
                                $marks_register->academic_id = getAcademicId();
                                $marks_register->save();
                                $marks_register->toArray();
                            } else {
                                // If already exists, it will updated
                                $pid = $previous_record->id;
                                $marks_register = SmMarkStore::find($pid);
                                $marks_register->total_marks = $mark_by_exam_part;

                                if (isset($absent_students)) {
                                    if (in_array($record_id, $absent_students)) {
                                        $marks_register->is_absent = 1;
                                    } else {
                                        $marks_register->is_absent = 0;
                                    }
                                }

                                $marks_register->teacher_remarks = gv($record, 'teacher_remarks');

                                $marks_register->save();
                            }

                            $exam_setup_count++;
                        } // end part insertion

                        $subject_full_mark = subjectFullMark($request->exam_id, $request->subject_id, $class_id, $section_id, $shift_id);
                        $student_obtained_mark = $total_marks_persubject;
                        $mark_by_persentage = subjectPercentageMark($student_obtained_mark, $subject_full_mark);

                        $mark_grade = SmMarksGrade::where([
                            ['percent_from', '<=', $mark_by_persentage],
                            ['percent_upto', '>=', $mark_by_persentage]])
                            ->where('academic_id', getAcademicId())
                            ->where('school_id', Auth::user()->school_id)
                            ->first();

                        $abc[] = $total_marks_persubject;

                        // $delete_reset_store = SmResultStore::where([
                        //         'class_id' => $class_id,
                        //         'section_id'=> $section_id,
                        //         'subject_id'=> $subject_id,
                        //         'exam_type_id'=> $exam_id,
                        //         'student_record_id'=> $record_id,
                        //         'student_id'=> $sid
                        //     ])
                        //     ->when(shiftEnable() && !empty($shift_id), function ($query) use ($shift_id) {
                        //         return $query->where('shift_id', $shift_id);
                        //     })
                        //     ->delete();

                        $previous_result_record = SmResultStore::where([
                            'class_id' => $class_id,
                            'section_id' => $section_id,
                            'subject_id' => $subject_id,
                            'exam_type_id' => $exam_id,
                            'student_record_id' => $record_id,
                            'student_id' => $sid,
                        ])
                            ->when(shiftEnable() && ! empty($shift_id), function ($query) use ($shift_id) {
                                return $query->where('shift_id', $shift_id);
                            })
                            ->first();

                        if (empty($previous_result_record)) {
                            // If not result exists, it will create
                            $result_record = new SmResultStore();
                            $result_record->class_id = $class_id;
                            $result_record->section_id = $section_id;
                            $result_record->shift_id = $shift_id;
                            $result_record->subject_id = $subject_id;
                            $result_record->exam_type_id = $exam_id;
                            $result_record->student_id = $sid;
                            $result_record->student_record_id = $record_id;

                            if (isset($absent_students)) {
                                if (in_array($record_id, $absent_students)) {
                                    $result_record->is_absent = 1;
                                } else {
                                    $result_record->is_absent = 0;
                                }
                            }
                            $result_record->total_marks = $total_marks_persubject;
                            $result_record->total_gpa_point = @$mark_grade->gpa;
                            $result_record->total_gpa_grade = @$mark_grade->grade_name;

                            $result_record->teacher_remarks = gv($record, 'teacher_remarks');

                            $result_record->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                            $result_record->school_id = Auth::user()->school_id;
                            $result_record->academic_id = getAcademicId();
                            $result_record->save();
                            $result_record->toArray();
                        } else {                               // If already result exists, it will updated
                            $id = $previous_result_record->id;
                            $result_record = SmResultStore::find($id);
                            $result_record->total_marks = $total_marks_persubject;
                            $result_record->total_gpa_point = @$mark_grade->gpa;
                            $result_record->total_gpa_grade = @$mark_grade->grade_name;
                            $result_record->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                            if (isset($absent_students)) {
                                if (in_array($record_id, $absent_students)) {
                                    $result_record->is_absent = 1;
                                } else {
                                    $result_record->is_absent = 0;
                                }
                            }

                            $result_record->teacher_remarks = gv($record, 'teacher_remarks');

                            $result_record->save();
                            $result_record->toArray();
                        }
                    }   // If student id is valid

                }
            }
            DB::commit();
            Toastr::success('Operation successful', 'Success');

            return redirect('marks-register-create');
        } catch (Exception $e) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function reportSearch(AddMarkRequest $request)
    {
        /*
        try {
        */
        if (moduleStatusCheck('University')) {

            $un_session = UnSession::find($request->un_session_id);
            $un_faculty = UnFaculty::find($request->un_faculty_id);
            $un_department = UnDepartment::find($request->un_department_id);
            $un_academic = UnAcademicYear::find($request->un_academic_id);
            $un_semester = UnSemester::find($request->un_semester_id);
            $un_semester_label = UnSemesterLabel::find($request->un_semester_label_id);
            $un_section = SmSection::find($request->un_section_id);

            $exam_type = $request->exam_type;
            $subject_id = $request->subject_id;
            $subjectName = UnSubject::find($subject_id);

            $examAttendance = SmExamAttendance::query();
            $exam_attendance = universityFilter($examAttendance, $request)
                ->where('un_subject_id', $subject_id)
                ->first();

            if ($exam_attendance) {
                $exam_attendance_child = SmExamAttendanceChild::where('exam_attendance_id', $exam_attendance->id)
                    ->first();
            } else {
                Toastr::error('Exam attendance Not Done Yet', 'Failed');

                return redirect()->back();
            }

            $StudentRecord = StudentRecord::query();
            $students = universityFilter($StudentRecord, $request)->get();

            $SmExamSchedule = SmExamSchedule::query();
            $exam_schedule = universityFilter($SmExamSchedule, $request)
                ->where('un_subject_id', $subject_id)
                ->first();
            $request = $request;

            if ($students->count() === 0) {
                Toastr::error('Sorry ! Student is not available Or exam schedule is not set yet.', 'Failed');

                return redirect()->back();
            }
            $SmExamSetup = SmExamSetup::query();
            $marks_entry_form = universityFilter($SmExamSetup, $request)
                ->where('exam_term_id', $exam_type)
                ->where('un_subject_id', $subject_id)
                ->get();
            $marks_registers = 1;

            if ($marks_entry_form->count() > 0) {
                $number_of_exam_parts = count($marks_entry_form);

                return view('backEnd.examination.masks_register', compact(
                    'students',
                    'number_of_exam_parts',
                    'marks_entry_form',
                    'marks_registers',
                    'exam_type',
                    'subject_id',
                    'un_session',
                    'un_faculty',
                    'un_department',
                    'un_academic',
                    'un_semester',
                    'un_semester_label',
                    'un_section',
                    'subjectName',
                    'request',
                ));
            }
            Toastr::error('Sorry ! Exam setup is not set yet.', 'Failed');

            return redirect()->back();

        }
        $exam = SmExam::query();
        $exam->where('exam_type_id', $request->exam)
            ->when($request->shift, function ($query, $shift) {
                $query->where('shift_id', $shift);
            })
            ->where('subject_id', $request->subject)
            ->where('class_id', $request->class);

        if (empty($request->section)) {
            $exam = $exam->first();
            if (! $exam) {
                Toastr::error('Sorry ! Exam setup is not set yet.', 'Failed');

                return redirect()->back();
            }

            $classSections = SmAssignSubject::where('class_id', $request->class)
                ->when($request->shift, function ($query, $shift) {
                    $query->where('shift_id', $shift);
                })
                ->where('subject_id', $request->subject)
                ->where('school_id', auth()->user()->school_id)
                ->where('academic_id', getAcademicId())
                ->get(['section_id']);

            $exam_attendance = SmExamAttendance::where('class_id', $request->class)
                ->where('exam_id', $exam->id)
                ->when($request->shift, function ($query, $shift) {
                    $query->where('shift_id', $shift);
                })
                ->where('subject_id', $request->subject)
                ->first();
        } else {
            $exam = $exam->where('section_id', $request->section)->first();
            if (! $exam) {
                Toastr::error('Sorry ! Exam setup is not set yet.', 'Failed');

                return redirect()->back();
            }

            $exam_attendance = SmExamAttendance::where('class_id', $request->class)->where('section_id', $request->section)
                ->when($request->shift, function ($query, $shift) {
                    $query->where('shift_id', $shift);
                })
                ->where('exam_id', $exam->id)->where('subject_id', $request->subject)->first();
        }

        if (empty($exam_attendance) && ! isSkip('exam_attendance')) {
            Toastr::error('Exam Attendance not taken yet, please check exam attendance', 'Failed');

            return redirect()->back();
        }

        $exams = SmExamType::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get();
        $classes = SmClass::get();
        $exam_types = SmExamType::get();
        $exam_id = $request->exam;
        $class_id = $request->class;
        $section_id = $request->section;
        $shift_id = $request->shift;
        $subject_id = $request->subject;
        $subjectNames = SmSubject::where('id', $subject_id)->first();
        $exam_type_id = $exam->exam_type_id;

        $exam_type = SmExamType::find($exam->examType->id);
        $class = SmClass::find($request->class);
        $section = SmSection::find($request->section);

        $search_info['exam_name'] = $exam->examType->title;
        $search_info['class_name'] = $class->class_name;
        if ($request->section ) {
            $search_info['section_name'] = $section->section_name;
        } else {
            $search_info['section_name'] = 'All Sections';
        }

        $students = StudentRecord::with('class', 'section')
            ->when($request->academic_year, function ($query) use ($request) {
                $query->where('academic_id', $request->academic_year);
            })
            ->when($request->class, function ($query) use ($request) {
                $query->where('class_id', $request->class);
            })
            ->when($request->section, function ($query) use ($request) {
                $query->where('section_id', $request->section);
            })
            ->when($request->shift, function ($query) use ($request) {
                $query->where('shift_id', $request->shift);
            })
            ->when(! $request->academic_year, function ($query) {
                $query->where('academic_id', getAcademicId());
            })->where('school_id', auth()->user()->school_id)->where('is_promote', 0)->whereHas('studentDetail', function ($q) {
                $q->where('active_status', 1);
            })->get()->sortBy('roll_no');

        $exam_schedule = SmExamSchedule::where('exam_id', $request->exam)->where('class_id', $request->class)->where('section_id', $request->section)->where('academic_id', getAcademicId())->first();
        if ($students->count() === 0) {
            Toastr::error('Sorry ! Student is not available Or exam schedule is not set yet.', 'Failed');

            return redirect()->back();
            // return redirect()->back()->with('message-danger', 'Sorry ! Student is not available Or exam schedule is not set yet.');
        }
        $marks_entry_form = SmExamSetup::query();
        if ($request->class !== null) {
            $marks_entry_form->where('exam_term_id', $exam_type->id)->where('class_id', $class_id);
        }
        if ($request->section !== null) {
            $marks_entry_form->where('section_id', $request->section);
        }
        $marks_entry_form = $marks_entry_form->where('subject_id', $subject_id)->where('academic_id', getAcademicId())->get();

        if ($marks_entry_form->count() > 0) {
            $number_of_exam_parts = count($marks_entry_form);

            return view('backEnd.examination.masks_register_search', compact('exam_type_id', 'exams', 'classes', 'students', 'exam_id', 'class_id', 'section_id', 'subject_id', 'subjectNames', 'number_of_exam_parts', 'marks_entry_form', 'exam_types', 'shift_id'));
        }
        Toastr::error('Sorry ! Exam setup is not set yet.', 'Failed');

        return redirect()->back();
        // return redirect()->back()->with('message-danger', 'Sorry ! Exam schedule is not set yet.');

        /*
        } catch (\Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
        }
        */
    }

    public function import()
    {
        /*
        try {
        */
        $exams = SmExamType::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get();
        $classes = SmClass::get();

        return view('backEnd.examination.masks_register_import', compact('exams', 'classes'));
        /*
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function importStore(Request $request)
    {
        ini_set('max_execution_time', 0);
        $file = $request->file('file');
        $step = $request->get('step', 'upload');

        if ($step === 'upload' || $step === 'map') {
            $validate_rules = [
                'file' => 'required|mimes:csv,xls,xlsx|max:2048',
            ];
        } elseif ($step === 'import') {
            $validate_rules = [
                'index' => ['required', 'array'],
            ];
        } else {
            $validate_rules = [
            ];
        }

        $request->validate($validate_rules, validationMessage($validate_rules));

        $exam_setup = SmExamSetup::where('exam_term_id', $request->exam_id)
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->where('subject_id', $request->subject_id)
            ->when($request->filled('shift_id') && shiftEnable(), function ($query) use ($request) {
                $query->where('shift_id', $request->shift_id);
            })
            ->get();
        $expectedHeaders = ['Admission No'];
        foreach ($exam_setup as $setup) {
            $title = $setup->exam_title;
            $expectedHeaders[] = $title;
        }
        $expectedHeaders[] = 'Teacher remarks';

        $requiredHeaders = ['admission_no'];
        foreach ($exam_setup as $setup) {
            $title = $setup->exam_title;
            $requiredHeaders[] = convertToSnakeCase($title);
        }

        $nameHeaders = ['Admission No'];
        foreach ($exam_setup as $setup) {
            $set_up = $setup->id;
            $nameHeaders[] = $set_up;
        }
        $nameHeaders[] = 'Teacher remarks';

        $exam = SmExamType::findOrFail($request->exam_id);
        $class = SmClass::findOrFail($request->class_id);
        $section = SmSection::findOrFail($request->section_id);
        $subject = SmSubject::findOrFail($request->subject_id);
        if (shiftEnable()) {
            $shift = Shift::findOrFail($request->shift_id);
        } else {
            $shift = '';
        }

        if ($step === 'upload') {
            $headers = (new HeadingRowImport())->toArray($file);

            $headers = $headers[0][0];
            $filteredHeaders = array_filter($headers, fn ($header): bool => ! is_numeric($header));
            $filteredHeaders = array_values($filteredHeaders);

            return view('backEnd.partials.register-import._map', ['filteredHeaders' => $filteredHeaders, 'expectedHeaders' => $expectedHeaders, 'file' => $file, 'exam' => $exam, 'class' => $class, 'section' => $section, 'subject' => $subject, 'shift' => $shift]);
        }
        if ($step === 'map') {
            $allData = Excel::toArray(new BulkImport(), $file)[0];
            $mappedHeaders = json_decode((string) $request->get('headers'));
            $url = route('marks-register-import-store');

            return view('backEnd.partials.register-import._import', ['expectedHeaders' => $expectedHeaders, 'allData' => $allData, 'mappedHeaders' => $mappedHeaders, 'url' => $url, 'requiredHeaders' => $requiredHeaders, 'exam' => $exam, 'class' => $class, 'section' => $section, 'subject' => $subject, 'nameHeaders' => $nameHeaders, 'shift' => $shift]);
        }
        // $result = $this->contactRepositories->csv_contact_upload($request->except('_token', 'step'));
        if ($step === 'import') {
            DB::beginTransaction();
            try {
                foreach ($request->markStore as $record_index => $record) {
                    $admission_no = gv($record, 'adimission_no');
                    $student = SmStudent::where('admission_no', $admission_no)->first();

                    if (! $student) {
                        continue;
                    }

                    $sid = $student->id;
                    $marks = gv($record, 'marks', []);
                    $roll_no = $student->roll_no;
                    $section_id = $request->section_id ?: gv($record, 'section');
                    $class_id = $request->class_id;
                    $shift_id = $request->shift_id ?? $request->shift_id;
                    // student_record_id
                    $student_record = DB::table('student_records')->where([
                        ['student_id', '=', $sid],
                        ['class_id', '=', $class_id],
                        ['section_id', '=', $section_id],
                        ['academic_id', '=', getAcademicId()],
                    ])
                        ->when(! empty($shift_id) && shiftEnable(), function ($query) use ($shift_id) {
                            $query->where('shift_id', $shift_id);
                        })->first();

                    if (! $student_record) {
                        continue;
                    }

                    $student_record_id = $student_record->id;

                    if (! empty($marks)) {
                        $total_marks_persubject = 0;

                        foreach ($marks as $exam_setup_id => $part_mark) {
                            $mark_by_exam_part = $part_mark ?? 0;
                            $total_marks_persubject += $mark_by_exam_part;

                            // Delete existing mark
                            SmMarkStore::where([
                                ['class_id', $class_id],
                                ['section_id', $section_id],
                                ['subject_id', $request->subject_id],
                                ['exam_term_id', $request->exam_id],
                                ['student_record_id', $student_record_id],
                                ['exam_setup_id', $exam_setup_id],
                                ['student_id', $sid],
                            ])->when(! empty($shift_id) && shiftEnable(), function ($query) use ($shift_id) {
                                $query->where('shift_id', $shift_id);
                            })->where('academic_id', getAcademicId())->delete();

                            // Insert new mark
                            $marks_register = new SmMarkStore();
                            $marks_register->exam_term_id = $request->exam_id;
                            $marks_register->class_id = $class_id;
                            $marks_register->section_id = $section_id;
                            $marks_register->shift_id = $request->shift_id ?? $request->shift_id;
                            $marks_register->subject_id = $request->subject_id;
                            $marks_register->student_id = $sid;
                            $marks_register->student_record_id = $student_record_id;
                            $marks_register->total_marks = $mark_by_exam_part;
                            $marks_register->exam_setup_id = $exam_setup_id;
                            $marks_register->is_absent = 0;
                            $marks_register->teacher_remarks = gv($record, 'teacher_remarks');
                            $marks_register->created_at = YearCheck::getYear().'-'.date('m-d H:i:s');
                            $marks_register->school_id = Auth::user()->school_id;
                            $marks_register->academic_id = getAcademicId();
                            $marks_register->save();

                        }

                        // Get grade
                        $subject_full_mark = subjectFullMark($request->exam_id, $request->subject_id, $class_id, $section_id, null);
                        $mark_by_percentage = subjectPercentageMark($total_marks_persubject, $subject_full_mark);

                        $mark_grade = SmMarksGrade::where([
                            ['percent_from', '<=', $mark_by_percentage],
                            ['percent_upto', '>=', $mark_by_percentage],
                        ])
                            ->where('academic_id', getAcademicId())
                            ->where('school_id', Auth::user()->school_id)
                            ->first();

                        // Save result
                        $previous_result_record = SmResultStore::where([
                            ['class_id', $class_id],
                            ['section_id', $section_id],
                            ['subject_id', $request->subject_id],
                            ['exam_type_id', $request->exam_id],
                            ['student_record_id', $student_record_id],
                            ['student_id', $sid],
                        ])->when(! empty($shift_id) && shiftEnable(), function ($query) use ($shift_id) {
                            $query->where('shift_id', $shift_id);
                        })->first();

                        if (! $previous_result_record) {
                            $result_record = new SmResultStore();
                        } else {
                            $result_record = $previous_result_record;
                        }

                        $result_record->class_id = $class_id;
                        $result_record->section_id = $section_id;
                        $result_record->subject_id = $request->subject_id;
                        $result_record->exam_type_id = $request->exam_id;
                        $result_record->shift_id = $request->shift_id ?? $request->shift_id;
                        $result_record->student_id = $sid;
                        $result_record->student_record_id = $student_record_id;
                        $result_record->is_absent = 0;
                        $result_record->total_marks = $total_marks_persubject;
                        $result_record->total_gpa_point = @$mark_grade->gpa;
                        $result_record->total_gpa_grade = @$mark_grade->grade_name;
                        $result_record->teacher_remarks = gv($record, 'teacher_remarks');
                        $result_record->created_at = YearCheck::getYear().'-'.date('m-d H:i:s');
                        $result_record->school_id = Auth::user()->school_id;
                        $result_record->academic_id = getAcademicId();
                        $result_record->save();
                    }
                }
                DB::commit();
                Toastr::success('Import file added successfully', 'success');
            } catch (Exception $e) {
                DB::rollBack();
                Toastr::success('Import file added failed', 'error');
            }

            return redirect()->route('marks_register_import');
        }
    }
}
