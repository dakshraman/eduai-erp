<?php

namespace App\Http\Controllers\Admin\StudentInfo;

use App\Http\Controllers\Controller;
use App\Models\ApiBaseMethod;
use App\Models\SmAcademicYear;
use App\Models\SmBaseSetup;
use App\Models\SmClass;
use App\Models\SmSection;
use App\Models\SmStaff;
use App\Models\SmStudent;
use App\Models\SmStudentAttendance;
use App\Models\SmStudentCategory;
use App\Models\StudentRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Modules\University\Repositories\Interfaces\UnCommonRepositoryInterface;

class SmStudentReportController extends Controller
{
    // this function call others
    public static function classSectionStudent($request)
    {

        if (moduleStatusCheck('University')) {
            return branchWise(StudentRecord::query()->when($request->un_academic_id, function ($query) use ($request): void {
                $query->where('un_academic_id', $request->un_academic_id);
            })
                ->when($request->un_faculty_id, function ($query) use ($request): void {
                    $query->where('un_faculty_id', $request->un_faculty_id);
                })
                ->when($request->un_department_id, function ($query) use ($request): void {
                    $query->where('un_department_id', $request->un_department_id);
                })
                ->when($request->un_semester_id, function ($query) use ($request): void {
                    $query->where('un_semester_id', $request->un_semester_id);
                })
                ->when($request->un_semester_label_id, function ($query) use ($request): void {
                    $query->where('un_semester_label_id', $request->un_semester_label_id);
                })
                ->when($request->un_section_id, function ($query) use ($request): void {
                    $query->where('un_section_id', $request->un_section_id);
                })
                ->where('school_id', auth()->user()->school_id)
                ->where('is_promote', 0)
                ->pluck('student_id')
                ->unique());

        } else {

            return branchWise(StudentRecord::query()->when($request->academic_year, function ($query) use ($request): void {
                $query->where('academic_id', $request->academic_year);
            })
                ->when($request->class, function ($query) use ($request): void {
                    $query->where('class_id', $request->class);
                })
                ->when($request->section, function ($query) use ($request): void {
                    $query->where('section_id', $request->section);
                })
                ->when($request->shift_id, function ($query) use ($request): void {
                    $query->where('shift_id', $request->shift_id);
                })
                ->when($request->branch_id && moduleStatusCheck('Branch') && Schema::hasColumn('student_records', 'branch_id'), function ($query) use ($request): void {
                    branchWiseApplyFilter($query, 'branch_id', $request->branch_id);
                })
                ->when(! $request->academic_year, function ($query): void {
                    $query->where('academic_id', getAcademicId());
                })->where('school_id', auth()->user()->school_id)->where('is_promote', 0)->pluck('student_id')->unique());

        }

    }

    public static function saasClassSectionStudent($request)
    {
        return branchWise(StudentRecord::withoutGlobalScopes()->when($request->academic_year, function ($query) use ($request): void {
            $query->where('academic_id', $request->academic_year);
        })
            ->when($request->class, function ($query) use ($request): void {
                $query->where('class_id', $request->class);
            })
            ->when($request->section, function ($query) use ($request): void {
                $query->where('section_id', $request->section);
            })
            ->when(! $request->academic_year, function ($query): void {
                $query->where('academic_id', SmAcademicYear::API_ACADEMIC_YEAR(auth()->user()->school_id));
            })->where('school_id', auth()->user()->school_id)->where('is_promote', 0)->pluck('student_id')->unique());
    }

    public static function classSectionAlumni($request)
    {
        return branchWise(StudentRecord::query()->when($request->academic_year, function ($query) use ($request): void {
            $query->where('academic_id', $request->academic_year);
        })
            ->when($request->class, function ($query) use ($request): void {
                $query->where('class_id', $request->class);
            })
            ->when($request->section, function ($query) use ($request): void {
                $query->where('section_id', $request->section);
            })
            ->when(! $request->academic_year, function ($query): void {
                $query->where('academic_id', getAcademicId());
            })->where('school_id', auth()->user()->school_id)->where('is_graduate', 1)->where('is_promote', 1)->pluck('student_id')->unique());
    }

    public static function SemesterLabelSectionStudent($request)
    {
        return branchWise(StudentRecord::query()->when($request->academic_year, function ($query) use ($request): void {
            $query->where('un_academic_id', $request->academic_year);
        })
            ->when($request->un_semester_label_id, function ($query) use ($request): void {
                $query->where('un_semester_label_id', $request->un_semester_label_id);
            })
            ->when($request->un_section_id, function ($query) use ($request): void {
                $query->where('un_section_id', $request->un_section_id);
            })
            ->when(! $request->academic_year, function ($query): void {
                $query->where('un_academic_id', getAcademicId());
            })->where('school_id', auth()->user()->school_id)->where('is_promote', 0)->pluck('student_id')->unique());
    }

    // studentReport modified by jmrashed
    public function studentReport(Request $request)
    {
        /*
        try {
        */
        $branch_id = moduleStatusCheck('Branch') ? userBranch() : null;
        $classes = SmClass::query()
            ->when($branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->get();
        $types = branchWise(SmStudentCategory::get());
        $genders = branchWise(SmBaseSetup::where('base_group_id', '=', '1')->get());

        return view('backEnd.studentInformation.student_report', ['classes' => $classes, 'types' => $types, 'genders' => $genders, 'branch_id' => $branch_id]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    // student report search modified by jmrashed
    public function studentReportSearch(Request $request)
    {
        if (moduleStatusCheck('University')) {
            $request->validate([
                'un_session_id' => 'required',
            ]);
        } else {
            $request->validate([
                'class_id' => 'required',
            ]);
        }

        /*
        try {
        */
        $data = [];
        $branch_id = $request->branch_id ?? (moduleStatusCheck('Branch') ? userBranch() : null);
        $student_records = StudentRecord::query();
        $student_records->where('school_id', Auth::user()->school_id)->whereHas('studentDetail', function ($q): void {
            $q->where('active_status', 1);
        });
        if ($request->class_id) {
            $student_records->where('class_id', $request->class_id);
        }

        if ($request->section_id) {
            $student_records->where('section_id', $request->section_id);
        }

        if ($request->shift) {
            $student_records->where('shift_id', $request->shift);
        }

        if ($branch_id) {
            branchWiseApplyFilter($student_records, 'branch_id', $branch_id);
        } else {
            $student_records = branchWise($student_records);
        }

        if (moduleStatusCheck('University')) {
            $student_records = universityFilter($student_records, $request);
        }

        $students = $student_records->with(['student' => function ($q) use ($request): void {
            $q->when($request->type, function ($q) use ($request): void {
                $q->where('student_category_id', $request->type);
            })->when($request->gender, function ($q) use ($request): void {
                $q->where('gender_id', $request->gender);
            })->where('active_status', 1);
        }])->whereHas('student', function ($q) use ($request): void {
            $q->when($request->type, function ($q) use ($request): void {
                $q->where('student_category_id', $request->type);
            })->when($request->gender, function ($q) use ($request): void {
                $q->where('gender_id', $request->gender);
            })->where('active_status', 1);
        })->get();

        $data['student_records'] = $students;
        $data['classes'] = SmClass::query()
            ->when($branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->get();
        $data['types'] = branchWise(SmStudentCategory::get());
        $data['genders'] = branchWise(SmBaseSetup::where('base_group_id', '=', '1')->get());
        $data['gender_id'] = $request->gender;
        $selected['class_id'] = $request->class_id;
        $selected['section_id'] = $request->section_id;
        $selected['branch_id'] = $branch_id;
        $selected['shift_id'] = shiftEnable() ? $request->shift : '';
        $data['type_id'] = $request->type;
        if (moduleStatusCheck('University')) {
            $interface = App::make(UnCommonRepositoryInterface::class);
            $data += $interface->getCommonData($request);
        }

        $data = is_array($data) ? $data : [];
        $selected = is_array($selected) ? $selected : [];
        return view('backEnd.studentInformation.student_report', array_merge($data, $selected));
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function studentAttendanceReport(Request $request)
    {
        /*
        try {
        */
        if (teacherAccess()) {
            $teacher_info = branchWise(SmStaff::where('user_id', Auth::user()->id)->first());
            $classes = $teacher_info->classes;
        } else {
            $classes = branchWise(SmClass::get());
        }

        $types = branchWise(SmStudentCategory::get());
        $genders = branchWise(SmBaseSetup::where('base_group_id', '=', '1')->get());

        return view('backEnd.studentInformation.student_attendance_report', ['classes' => $classes, 'types' => $types, 'genders' => $genders]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function studentAttendanceReportSearch(Request $request)
    {

        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
            'month' => 'required',
            'year' => 'required',
        ]);

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        /*
        try {
        */
        $year = $request->year;
        $month = $request->month;
        $class_id = $request->class;
        $section_id = $request->section;
        $current_day = date('d');
        $clas = SmClass::findOrFail($request->class);
        $sec = SmSection::findOrFail($request->section);
        $days = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);
        if (teacherAccess()) {
            $teacher_info = SmStaff::where('user_id', Auth::user()->id)->first();
            $classes = $teacher_info->classes;
        } else {
            $classes = branchWise(SmClass::get());
        }

        $students = branchWise(SmStudent::where('class_id', $request->class)
            ->where('section_id', $request->section)->get());

        $attendances = [];
        foreach ($students as $student) {
            $attendance = branchWise(SmStudentAttendance::where('student_id', $student->id)->where('attendance_date', 'like', $request->year.'-'.$request->month.'%')->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get());
            if (count($attendance) !== 0) {
                $attendances[] = $attendance;
            }
        }

        return view('backEnd.studentInformation.student_attendance_report', ['classes' => $classes, 'attendances' => $attendances, 'students' => $students, 'days' => $days, 'year' => $year, 'month' => $month, 'current_day' => $current_day, 'class_id' => $class_id, 'section_id' => $section_id, 'clas' => $clas, 'sec' => $sec]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function studentAttendanceReportPrint($class_id, $section_id, string $month, string $year)
    {
        set_time_limit(2700);
        /*
        try {
        */
        $current_day = date('d');
        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $students = branchWise(SmStudent::where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->get());

        $attendances = [];
        foreach ($students as $student) {
            $attendance = branchWise(SmStudentAttendance::where('student_id', $student->id)
                ->where('attendance_date', 'like', $year.'-'.$month.'%')
                ->get());

            if ($attendance) {
                $attendances[] = $attendance;
            }
        }

        // $pdf = Pdf::loadView(
        //     'backEnd.studentInformation.student_attendance_print',
        //     [
        //         'attendances' => $attendances,
        //         'days' => $days,
        //         'year' => $year,
        //         'month' => $month,
        //         'class_id' => $class_id,
        //         'section_id' => $section_id,
        //         'class' => SmClass::find($class_id),
        //         'section' => SmSection::find($section_id),
        //     ]
        // )->setPaper('A4', 'landscape');
        // return $pdf->stream('student_attendance.pdf');

        $class = SmClass::find($class_id);
        $section = SmSection::find($section_id);

        return view('backEnd.studentInformation.student_attendance_print', ['class' => $class, 'section' => $section, 'attendances' => $attendances, 'days' => $days, 'year' => $year, 'month' => $month, 'current_day' => $current_day, 'class_id' => $class_id, 'section_id' => $section_id]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function guardianReport(Request $request)
    {
        /*
        try {
        */
        $branch_id = moduleStatusCheck('Branch') ? userBranch() : null;
        $classes = SmClass::query()
            ->select(['class_name', 'id'])
            ->when($branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->get();

        return view('backEnd.studentInformation.guardian_report', ['classes' => $classes, 'branch_id' => $branch_id]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function guardianReportSearch(Request $request)
    {
        $input = $request->all();
        if (moduleStatusCheck('University')) {
            $validator = Validator::make($input, [
                'un_session_id' => 'required',
            ]);
        } else {
            $validator = Validator::make($input, [
                'class_id' => 'required',
            ], [
                'class_id' => 'The Class field is required.',
            ]);
        }

        if ($validator->fails()) {
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        /*
        try {
        */
        $branch_id = $request->branch_id ?? (moduleStatusCheck('Branch') ? userBranch() : null);
        if (moduleStatusCheck('University')) {
            $student_records = branchWise(StudentRecord::with('student', 'student.parents', 'UnSemesterLabel', 'unDepartment', 'class:id,class_name', 'section:id,section_name'));
        } else {
            $student_records = branchWise(StudentRecord::with('student', 'student.parents', 'class:id,class_name', 'section:id,section_name'));
        }

        $student_records->where('school_id', Auth::user()->school_id);
        if ($request->class_id) {
            $student_records->where('class_id', $request->class_id);
        }

        if ($request->section_id) {
            $student_records->where('section_id', $request->section_id);
        }

        if ($request->shift) {
            $student_records->where('shift_id', $request->shift);
        }

        if ($branch_id) {
            branchWiseApplyFilter($student_records, 'branch_id', $branch_id);
        }

        if (moduleStatusCheck('University')) {
            $student_records = universityFilter($student_records, $request);
        }
        if (moduleStatusCheck('Branch')) {
            $students = $student_records->select(['id', 'student_id', 'class_id', 'section_id', 'branch_id'])->get();
        } else {
            $students = $student_records->select(['id', 'student_id', 'class_id', 'section_id'])->get();
        }
        $data = [];
        $data['student_records'] = $students;
        $data['shift_id'] = shiftEnable() ? $request->shift : '';
        $data['classes'] = SmClass::query()
            ->select(['class_name', 'id'])
            ->when($branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->get();
        $data['branch_id'] = $branch_id;

        $data['class_id'] = $request->class_id;
        $data['section_id'] = $request->section_id;

        return view('backEnd.studentInformation.guardian_report')->with($data);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function studentLoginReport(Request $request)
    {
        /*
        try {
        */
        $classes = branchWise(SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get());

        return view('backEnd.studentInformation.login_info', ['classes' => $classes]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function studentLoginSearch(Request $request)
    {

        $request->all();
        if (moduleStatusCheck('University')) {
            $request->validate([
                'un_session_id' => 'required',
            ]);
        } else {
            $request->validate([
                'class' => 'required',
            ]);
        }
        /*
        try {
        */
        $data = [];
        $student_records = branchWise(StudentRecord::query());
        $student_records->where('school_id', Auth::user()->school_id);
        if ($request->class) {
            $student_records->where('class_id', $request->class);
        }

        if ($request->section) {
            $student_records->where('section_id', $request->section);
        }

        if ($request->shift) {
            $student_records->where('shift_id', $request->shift);
        }

        if ($request->branch_id) {
            $student_records->where('branch_id', $request->branch_id);
        }

        if (moduleStatusCheck('University')) {
            $student_records = universityFilter($student_records, $request);
        }

        $students = $student_records->with('student.user', 'student.parents')->get();
        $data['student_records'] = $students;
        $data['classes'] = branchWise(SmClass::get());
        $data['class_id'] = $request->class;
        $data['section_id'] = $request->section;
        $data['shift_id'] = shiftEnable() ? $request->shift : '';
        $data['branch_id'] = $request->branch_id;
        $data['clas'] = SmClass::find($request->class);
        if (moduleStatusCheck('University')) {
            $interface = App::make(UnCommonRepositoryInterface::class);
            $data += $interface->getCommonData($request);
        }

        return view('backEnd.studentInformation.login_info', $data);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function studentHistory(Request $request)
    {
        /*
        try {
        */
        $branch_id = moduleStatusCheck('Branch') ? userBranch() : null;
        $classes = SmClass::query()
            ->select(['id', 'class_name'])
            ->when($branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->get();
        $years = branchWise(SmStudent::select('admission_date')->where('active_status', 1)
            ->where('academic_id', getAcademicId())->get()
            ->groupBy(function ($val): string {
                return Carbon::parse($val->admission_date)->format('Y');
            }));

        return view('backEnd.studentInformation.student_history', ['classes' => $classes, 'years' => $years, 'branch_id' => $branch_id]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function studentHistorySearch(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        /*
        try {
        */
        $academic_id = getAcademicId();
        $user = Auth::user();
        $branch_id = $request->branch_id ?? (moduleStatusCheck('Branch') ? userBranch() : null);
        $request->merge([
            'branch_id' => $branch_id,
            'shift_id' => shiftEnable() ? $request->shift : null,
        ]);
        $student_ids = static::classSectionStudent($request);
        $classes = SmClass::query()
            ->where('active_status', 1)
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->when($branch_id, function ($query) use ($branch_id) {
                return branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->get();
        $students = branchWise(SmStudent::where('academic_id', $academic_id)
            ->where('active_status', 1));
        if ($request->admission_year ) {
            $students = $students->where('admission_date', 'like', $request->admission_year.'%');
        }

        $students = $students->whereIn('id', $student_ids)
            ->with('recordClass.class', 'parents:id,guardians_name,guardians_mobile', 'promotion', 'session')
            ->where('academic_id', $academic_id)
            ->where('school_id', $user->school_id)
            ->when(moduleStatusCheck('Branch') && $branch_id, function ($query) use ($branch_id) {
                branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->select(array_merge(
                ['admission_no', 'id', 'class_id', 'section_id', 'parent_id', 'mobile', 'admission_date', 'first_name', 'last_name'],
                moduleStatusCheck('Branch') ? ['branch_id'] : []
            ))
            ->get();

        $years = branchWise(SmStudent::select('admission_date')->where('active_status', 1)
            ->where('academic_id', $academic_id)->get()
            ->groupBy(function ($val): string {
                return Carbon::parse($val->admission_date)->format('Y');
            }));
        $class_id = $request->class;
        $shift_id = shiftEnable() ? $request->shift : '';
        $year = $request->admission_year;
        $student_id = null;

        $clas = SmClass::find($request->class);

        return view('backEnd.studentInformation.student_history', ['students' => $students, 'classes' => $classes, 'years' => $years, 'class_id' => $class_id, 'year' => $year, 'clas' => $clas, 'student_id' => $student_id, 'shift_id' => $shift_id, 'branch_id' => $branch_id]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }
}
