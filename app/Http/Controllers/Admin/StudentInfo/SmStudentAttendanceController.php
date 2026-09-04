<?php

namespace App\Http\Controllers\Admin\StudentInfo;

use App\Http\Controllers\Admin\SystemSettings\SmSystemSettingController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StudentInfo\SmStudentAttendanceSearchRequest;
use App\Imports\StudentAttendanceImport;
use App\Jobs\TakeStudentAttendance;
use App\Models\Shift;
use App\Models\SmClass;
use App\Models\SmClassSection;
use App\Models\SmNotification;
use App\Models\SmParent;
use App\Models\SmSection;
use App\Models\SmStaff;
use App\Models\SmStudent;
use App\Models\SmStudentAttendance;
use App\Models\StudentAttendanceBulk;
use App\Models\StudentRecord;
use App\Traits\NotificationSend;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Branch\Entities\Branch;
use Modules\University\Imports\StudentAttendanceImport as UniversityStudentAttendanceImport;
use Modules\University\Repositories\Interfaces\UnCommonRepositoryInterface;

class SmStudentAttendanceController extends Controller
{
    use NotificationSend;

    public static function activeStudent()
    {
        return SmStudent::where('active_status', 1)
            ->where('school_id', Auth::user()->school_id)
            ->get();
    }

    public function index(Request $request)
    {
        /*
        try {
        */
        if (teacherAccess()) {
            $teacher_info = SmStaff::where('user_id', auth()->user()->id)->first();
            $classes = $teacher_info->classes;
        } else {
            $classes = SmClass::get();
        }

        return view('backEnd.studentInformation.student_attendance', ['classes' => $classes]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function studentSearch(SmStudentAttendanceSearchRequest $smStudentAttendanceSearchRequest)
    {

        /*
        try {
        */
        $date = $smStudentAttendanceSearchRequest->attendance_date;
        if (teacherAccess()) {
            $teacher_info = SmStaff::where('user_id', auth()->user()->id)->first();
            $classes = $teacher_info->classes;
        } else {
            $classes = SmClass::get();
        }

        $students = branchWise(StudentRecord::with('studentDetail', 'studentDetail.DateWiseAttendances')
            ->when($smStudentAttendanceSearchRequest->class_id, function ($query) use ($smStudentAttendanceSearchRequest): void {
                $query->where('class_id', $smStudentAttendanceSearchRequest->class_id);
            })
            ->whereHas('studentDetail', function ($q): void {
                $q->where('active_status', 1);
            })
            ->when($smStudentAttendanceSearchRequest->section_id, function ($query) use ($smStudentAttendanceSearchRequest): void {
                $query->where('section_id', $smStudentAttendanceSearchRequest->section_id);
            })
            ->when($smStudentAttendanceSearchRequest->shift, function ($query) use ($smStudentAttendanceSearchRequest) {
                $query->where('shift_id', $smStudentAttendanceSearchRequest->shift);
            })
            ->when($this->canUseStudentRecordBranch() && $smStudentAttendanceSearchRequest->branch_id, function ($query) use ($smStudentAttendanceSearchRequest): void {
                branchWiseApplyFilter($query, 'branch_id', $smStudentAttendanceSearchRequest->branch_id);
            })->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get()->sortBy('roll_no'));

        if (moduleStatusCheck('University')) {
            $model = branchWise(StudentRecord::query());
            $students = universityFilter($model, $smStudentAttendanceSearchRequest)
                ->with('studentDetail', 'studentDetail.DateWiseAttendances')
                ->get()->sortBy('roll_no');
        }

        if ($students->isEmpty()) {
            Toastr::error('No Result Found', 'Failed');

            return redirect('student-attendance');
        }

        $attendance_type = $students[0]['studentDetail']['DateWiseAttendances'] !== null ? $students[0]['studentDetail']['DateWiseAttendances']['attendance_type'] : '';

        $class_id = $smStudentAttendanceSearchRequest->class_id;
        $section_id = $smStudentAttendanceSearchRequest->section_id;
        $class_id = $smStudentAttendanceSearchRequest->class_id;
        $section_id = $smStudentAttendanceSearchRequest->section_id;
        $shift_id = $smStudentAttendanceSearchRequest->shift ?? null;
        $branch_id = $smStudentAttendanceSearchRequest->branch_id ?? null;
        if (moduleStatusCheck('University')) {
            $interface = App::make(UnCommonRepositoryInterface::class);
            $search_info = $interface->searchInfo($smStudentAttendanceSearchRequest);
            $search_info += $interface->oldValueSelected($smStudentAttendanceSearchRequest);
        } else {
            $search_info['class_name'] = SmClass::query()->select(['class_name'])->find($smStudentAttendanceSearchRequest->class_id)?->class_name;
            $search_info['section_name'] = SmSection::query()->select(['section_name'])->find($smStudentAttendanceSearchRequest->section_id)?->section_name;
            $search_info['shift_name'] = Shift::query()->select(['name'])->find($smStudentAttendanceSearchRequest->shift)?->name;
        }
        if (moduleStatusCheck('Branch')) {
            $search_info['branch_name'] = '';
            if (Schema::hasTable('branches') && $smStudentAttendanceSearchRequest->branch_id) {
                $search_info['branch_name'] = Branch::query()->select(['branch_name'])->find($smStudentAttendanceSearchRequest->branch_id)?->branch_name ?? '';
            }
        }
        $search_info['date'] = $smStudentAttendanceSearchRequest->attendance_date;
        $sections = branchWise(SmClassSection::with(['sectionName' => function ($query) {
            return $query->select(['section_name']);
        }])->where('class_id', $smStudentAttendanceSearchRequest->class_id)->get());

        $un_session_id = $smStudentAttendanceSearchRequest->un_session_id;
        $un_faculty_id = $smStudentAttendanceSearchRequest->un_faculty_id;
        $un_department_id = $smStudentAttendanceSearchRequest->un_department_id;
        $un_academic_id = $smStudentAttendanceSearchRequest->un_academic_id;
        $un_semester_id = $smStudentAttendanceSearchRequest->un_semester_id;
        $un_semester_label_id = $smStudentAttendanceSearchRequest->un_semester_label_id;
        $un_section_id = $smStudentAttendanceSearchRequest->un_section_id;

        $date_modified = \Carbon\Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');
        foreach ($students as $student) {
            $leave_request = \App\Models\SmLeaveRequest::where('staff_id', $student->student->user_id)
                ->where('approve_status', 'A')
                ->whereDate('leave_from', '<=', $date_modified)
                ->whereDate('leave_to', '>=', $date_modified)
                ->first();
            $student->is_on_leave = ! empty($leave_request);
            $student->leave_reason = $leave_request?->reason;
        }

        return view('backEnd.studentInformation.student_attendance', ['classes' => $classes, 'sections' => $sections, 'class_id' => $class_id, 'section_id' => $section_id, 'date' => $date, 'students' => $students, 'attendance_type' => $attendance_type, 'search_info' => $search_info, 'class_id' => $class_id, 'section_id' => $section_id, 'shift_id' => $shift_id, 'un_session_id' => $un_session_id, 'un_faculty_id' => $un_faculty_id, 'un_department_id' => $un_department_id, 'un_academic_id' => $un_academic_id, 'un_semester_id' => $un_semester_id, 'un_semester_label_id' => $un_semester_label_id, 'un_section_id' => $un_section_id, 'branch_id' => $branch_id])->with($search_info);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function studentAttendanceStore(Request $request)
    {
        /*
        try {
        */

        $attendance_info = [];
        foreach ($request->attendance as $record_id => $student) {
            if (moduleStatusCheck('University')) {
                $attendance = SmStudentAttendance::where('student_id', gv($student, 'student'))
                    ->where('attendance_date', date('Y-m-d', strtotime($request->date)))
                    ->when(gv($student, 'un_session_id'), function ($q) use ($student): void {
                        $q->where('un_session_id', gv($student, 'un_session_id'));
                    })
                    ->when(gv($student, 'un_faculty_id'), function ($q) use ($student): void {
                        $q->where('un_faculty_id', gv($student, 'un_faculty_id'));
                    })
                    ->when(gv($student, 'un_department_id'), function ($q) use ($student): void {
                        $q->where('un_department_id', gv($student, 'un_department_id'));
                    })
                    ->when(gv($student, 'un_semester_id'), function ($q) use ($student): void {
                        $q->where('un_academic_id', gv($student, 'un_academic_id'));
                    })
                    ->when(gv($student, 'un_semester_id'), function ($q) use ($student): void {
                        $q->where('un_semester_id', gv($student, 'un_semester_id'));
                    })
                    ->when(gv($student, 'un_semester_label_id'), function ($q) use ($student): void {
                        $q->where('un_semester_label_id', gv($student, 'un_semester_label_id'));
                    })
                    ->when(gv($student, 'un_section_id'), function ($q) use ($student): void {
                        $q->where('un_section_id', gv($student, 'un_section_id'));
                    })
                    ->when($this->canUseAttendanceBranch() && gv($student, 'branch_id'), function ($q) use ($student): void {
                        branchWiseApplyFilter($q, 'branch_id', gv($student, 'branch_id'));
                    })

                    ->where('student_record_id', $record_id)
                    ->where('school_id', Auth::user()->school_id)->first();
            } else {
                $attendance = SmStudentAttendance::where('student_id', gv($student, 'student'))
                    ->where('attendance_date', date('Y-m-d', strtotime($request->date)))

                    ->when(! moduleStatusCheck('University'), function ($query) use ($student): void {
                        $query->where('class_id', gv($student, 'class'));
                    })->when(! moduleStatusCheck('University'), function ($query) use ($student): void {
                        $query->where('section_id', gv($student, 'section'));
                    })->when(! moduleStatusCheck('University') && shiftEnable(), function ($query) use ($student): void {
                        $query->where('shift_id', gv($student, 'shift'));
                    })->when($this->canUseAttendanceBranch() && gv($student, 'branch_id'), function ($query) use ($student): void {
                        branchWiseApplyFilter($query, 'branch_id', gv($student, 'branch_id'));
                    })
                    ->where('student_record_id', $record_id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)->first();
            }

            if ($attendance) {
                $attendance->delete();
            }

            $attendance = new SmStudentAttendance();

            $attendance_info[$record_id]['student_record_id'] = $record_id;
            $attendance_info[$record_id]['student_id'] = gv($student, 'student');
            $attendance_info[$record_id]['class_id'] = gv($student, 'class');
            $attendance_info[$record_id]['section_id'] = gv($student, 'section');
            if ($this->canUseAttendanceBranch()) {
                $attendance_info[$record_id]['branch_id'] = gv($student, 'branch_id');
            }
            if (shiftEnable()) {
                $attendance_info[$record_id]['shift_id'] = gv($student, 'shift');
            }
            $attendance_info[$record_id]['section_id'] = gv($student, 'section');
            if (property_exists($request, 'mark_holiday') && $request->mark_holiday !== null) {
                $attendance_info[$record_id]['attendance_type'] = 'H';
            } else {
                $attendance_info[$record_id]['attendance_type'] = gv($student, 'attendance_type');
                $attendance_info[$record_id]['notes'] = gv($student, 'note');
            }
            $attendance_info[$record_id]['attendance_date'] = date('Y-m-d', strtotime($request->date));
            $attendance_info[$record_id]['school_id'] = auth()->user()->school_id;
            $attendance_info[$record_id]['academic_id'] = getAcademicId();
            if (moduleStatusCheck('University')) {
                $attendance_info[$record_id]['un_academic_id'] = gv($student, 'un_academic_id');
                $attendance_info[$record_id]['un_session_id'] = gv($student, 'un_session_id');
                $attendance_info[$record_id]['un_department_id'] = gv($student, 'un_department_id');
                $attendance_info[$record_id]['un_faculty_id'] = gv($student, 'un_faculty_id');
                $attendance_info[$record_id]['un_semester_id'] = gv($student, 'un_semester_id');
                $attendance_info[$record_id]['un_semester_label_id'] = gv($student, 'un_semester_label_id');
                $attendance_info[$record_id]['un_section_id'] = gv($student, 'un_section_id');

            }

        }

        TakeStudentAttendance::dispatch($attendance_info);

        Toastr::success('Operation successful', 'Success');

        return redirect('student-attendance');
        /*
        } catch (Exception $exception) {

            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function studentAttendanceHoliday(Request $request)
    {
        if (moduleStatusCheck('University')) {
            $interface = App::make(UnCommonRepositoryInterface::class);
            $studentRecords = $interface->searchStudentRecord($request)->get();
        } else {
            $studentRecords = StudentRecord::where('class_id', $request->class_id)
                ->where('section_id', $request->section_id)
                ->when($request->shift_id, function ($query) use ($request) {
                    $query->where('shift_id', $request->shift_id);
                })
                ->when($this->canUseStudentRecordBranch() && $request->branch_id, function ($query) use ($request): void {
                    branchWiseApplyFilter($query, 'branch_id', $request->branch_id);
                })
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get();
        }

        if ($studentRecords->isEmpty()) {
            Toastr::error('No Result Found', 'Failed');

            return redirect('student-attendance');
        }

        foreach ($studentRecords as $studentRecord) {

            $attendance = SmStudentAttendance::where('student_id', $studentRecord->student_id)
                ->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))
                ->when(! moduleStatusCheck('University'), function ($query) use ($request): void {
                    $query->where('class_id', $request->class_id);
                })->when(! moduleStatusCheck('University'), function ($query) use ($request): void {
                    $query->where('section_id', $request->section_id);
                })->when(! moduleStatusCheck('University') && shiftEnable(), function ($query) use ($request): void {
                    $query->where('shift_id', $request->shift_id);
                })
                ->when($this->canUseAttendanceBranch() && $request->branch_id, function ($query) use ($request): void {
                    branchWiseApplyFilter($query, 'branch_id', $request->branch_id);
                })
                ->where('academic_id', getAcademicId())
                ->where('student_record_id', $studentRecord->id)
                ->where('school_id', Auth::user()->school_id)
                ->first();
            if (! empty($attendance)) {
                $attendance->delete();
            }

            if ($request->purpose === 'mark') {
                $attendance = new SmStudentAttendance();
                $attendance->attendance_type = 'H';
                $attendance->notes = 'Holiday';
                $attendance->attendance_date = date('Y-m-d', strtotime($request->attendance_date));
                $attendance->student_id = $studentRecord->student_id;
                $attendance->student_record_id = $studentRecord->id;
                $attendance->class_id = $studentRecord->class_id;
                $attendance->section_id = $studentRecord->section_id;
                $attendance->shift_id = shiftEnable() ? $studentRecord->shift_id : '';
                if ($this->canUseAttendanceBranch()) {
                    $attendance->branch_id = $studentRecord->branch_id ?? null;
                }
                $attendance->academic_id = getAcademicId();
                $attendance->school_id = Auth::user()->school_id;
                if (moduleStatusCheck('University')) {
                    $interface = App::make(UnCommonRepositoryInterface::class);
                    $interface->storeUniversityData($attendance, $request);
                }

                $attendance->save();

                $compact['holiday_date'] = date('Y-m-d', strtotime($request->attendance_date));
                @send_sms($studentRecord->student->mobile, 'holiday', $compact);
                @send_sms(@$studentRecord->student->parents->guardians_mobile, 'holiday', $compact);

                // futter notification
                $messege = '';
                $student = SmStudent::find($studentRecord->student_id);
                if ($student) {
                    $messege = app('translator')->get('student.Your_teacher_has_marked_holiday_in_the_attendance_on ', ['date' => dateconvert($attendance->attendance_date)]);
                    $notification = new SmNotification();
                    $notification->user_id = $student->user_id;
                    $notification->role_id = 2;
                    $notification->date = date('Y-m-d');
                    $notification->message = $messege;
                    $notification->school_id = Auth::user()->school_id;
                    $notification->academic_id = getAcademicId();
                    $notification->save();
                    try {
                        if ($student->user) {
                            $title = app('translator')->get('student.attendance_notication');

                            $notificationData = [
                                'id' => $student->user->id,
                                'title' => $title,
                                'body' => $notification->message,
                            ];

                            $systemSettingController = new SmSystemSettingController();
                            $systemSettingController->flutterNotificationApi(new Request($notificationData));
                        }
                    } catch (Exception $e) {
                        Log::info($e->getMessage());
                    }

                    $parent = SmParent::find($student->parent_id);
                    if ($parent) {
                        $messege = app('translator')->get('student.Your_child_is_marked_holiday_in_the_attendance_on_date', ['date' => dateConvert($attendance->attendance_date), 'student_name' => $student->full_name."'s"]);
                        $notification = new SmNotification();
                        $notification->user_id = $parent->user_id;
                        $notification->role_id = 3;
                        $notification->date = date('Y-m-d');
                        $notification->message = $messege;
                        $notification->school_id = Auth::user()->school_id;
                        $notification->academic_id = getAcademicId();
                        $notification->save();

                        try {
                            if ($parent->parent_user) {
                                $title = app('translator')->get('student.holiday_notification');

                                $notificationData = [
                                    'id' => $parent->parent_user->id,
                                    'title' => $title,
                                    'body' => $notification->message,
                                ];
                                $systemSettingController = new SmSystemSettingController();
                                $systemSettingController->flutterNotificationApi(new Request($notificationData));
                            }
                        } catch (Exception $e) {
                            Log::info($e->getMessage());
                        }
                    }
                    $compact['holiday_date'] = date('Y-m-d', strtotime($request->attendance_date));
                    @send_sms($studentRecord->student->mobile, 'holiday', $compact);
                }
            }
        }

        Toastr::success('Operation successful', 'Success');

        return redirect()->back();
    }

    public function studentAttendanceImport()
    {

        /*
        try {
        */
        $classes = branchWise(SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get());

        return view('backEnd.studentInformation.student_attendance_import', ['classes' => $classes]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    private function canUseStudentRecordBranch(): bool
    {
        return moduleStatusCheck('Branch') && Schema::hasColumn('student_records', 'branch_id');
    }

    private function canUseAttendanceBranch(): bool
    {
        return moduleStatusCheck('Branch') && Schema::hasColumn('sm_student_attendances', 'branch_id');
    }

    public function downloadStudentAtendanceFile()
    {

        /*
        try {
        */
        $studentsArray = ['admission_no', 'class_id', 'section_id', 'attendance_date', 'in_time', 'out_time'];

        return Excel::create('student_attendance_sheet', function ($excel) use ($studentsArray): void {
            $excel->sheet('student_attendance_sheet', function ($sheet) use ($studentsArray): void {
                $sheet->fromArray($studentsArray);
            });
        })->download('xlsx');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function studentAttendanceBulkStore(Request $request)
    {

        if (moduleStatusCheck('University')) {
            $request->validate([
                'un_session_id' => 'sometimes|nullable',
                'un_faculty_id' => 'required',
                'un_department_id' => 'required',
                'un_academic_id' => 'required',
                'un_semester_id' => 'required',
                'un_semester_label_id' => 'required',
                'un_section_id' => 'required',
                'file' => 'required',
                'attendance_date' => 'required',
            ]);
        } else {
            $request->validate([
                'attendance_date' => 'required',
                'file' => 'required|mimes:xlsx,csv',
                'class' => 'required',
                'section' => 'required',
            ]);
        }

        /*
        try {
        */
        if (moduleStatusCheck('University')) {
            Excel::import(new UniversityStudentAttendanceImport($request->un_session_id, $request->un_faculty_id, $request->un_department_id, $request->un_academic_id, $request->un_semester_id, $request->un_semester_label_id, $request->un_section_id), $request->file('file'), 's3', \Maatwebsite\Excel\Excel::XLSX);
        } else {
            Excel::import(new StudentAttendanceImport($request->class, $request->section), $request->file('file'), 's3', \Maatwebsite\Excel\Excel::XLSX);
        }
        $data = StudentAttendanceBulk::get();

        if (! empty($data)) {
            $class_sections = [];
            $university_data = [];
            foreach ($data as $value) {
                if (date('d/m/Y', strtotime($request->attendance_date)) === date('d/m/Y', strtotime($value->attendance_date))) {
                    $class_sections[] = $value->class_id.'-'.$value->section_id;
                }

                if (moduleStatusCheck('University')) {
                    $university_data[] = $value->un_session_id.'-'.$value->un_faculty_id.'-'.
                        $value->un_department_id.'-'.$value->un_academic_id.'-'.
                        $value->un_semester_id.'-'.$value->un_semester_label_id.'-'.$value->un_section_id;
                }
            }

            $present_students = [];
            $uniquesVales = moduleStatusCheck('University') ? $university_data : $class_sections;
            foreach (array_unique($uniquesVales) as $value) {
                if (moduleStatusCheck('University')) {
                    $universityData = explode('-', $value);
                    $students = branchWise(StudentRecord::where('un_session_id', $universityData[0])
                        ->where('un_faculty_id', $universityData[1])
                        ->where('un_department_id', $universityData[2])
                        ->where('un_academic_id', $universityData[3])
                        ->where('un_semester_id', $universityData[4])
                        ->where('un_semester_label_id', $universityData[5])
                        ->where('un_section_id', $universityData[6])
                        ->where('school_id', Auth::user()->school_id)
                        ->get());
                } else {
                    $class_section = explode('-', $value);
                    $students = branchWise(StudentRecord::where('class_id', $class_section[0])->where('section_id', $class_section[1])->where('school_id', Auth::user()->school_id)->get());
                }

                foreach ($students as $student) {
                    StudentAttendanceBulk::where('student_record_id', $student->id)->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))
                        ->delete();
                }
            }
            /*
            try {
            */
            foreach ($data as $value) {
                if ($value) {
                    if (date('d/m/Y', strtotime($request->attendance_date)) === date('d/m/Y', strtotime($value->attendance_date))) {
                        $student = StudentRecord::where('id', $value->student_record_id)->where('school_id', Auth::user()->school_id)->first();
                        if ($student) {
                            // SmStudentAttendance
                            $attendance_check = SmStudentAttendance::where('student_record_id', $student->id)
                                ->where('attendance_date', date('Y-m-d', strtotime($value->attendance_date)))->first();
                            if ($attendance_check) {
                                $attendance_check->delete();
                            }

                            $import = new SmStudentAttendance();
                            $import->student_id = $student->student_id;
                            $import->student_record_id = $student->id;
                            $import->attendance_date = date('Y-m-d', strtotime($request->attendance_date));
                            $import->attendance_type = $value->attendance_type;
                            $import->notes = $value->note;
                            $import->school_id = Auth::user()->school_id;
                            $import->academic_id = getAcademicId();
                            if (moduleStatusCheck('University')) {
                                $import->un_academic_id = $value->un_academic_id;
                                $import->un_session_id = $value->un_session_id;
                                $import->un_department_id = $value->un_department_id;
                                $import->un_faculty_id = $value->un_faculty_id;
                                $import->un_semester_id = $value->un_semester_id;
                                $import->un_semester_label_id = $value->un_semester_label_id;
                                $import->un_section_id = $value->un_section_id;
                            }

                            $import->save();
                        }
                    } else {
                        StudentAttendanceBulk::where('student_id', $value->student_id)->delete();
                    }
                }
            }
            /*
            } catch (Exception $e) {
                Toastr::error('Operation Failed', 'Failed');
                return redirect()->back();
            }
            */
            Toastr::success('Operation successful', 'Success');

            return redirect()->back();
        }
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }
}
