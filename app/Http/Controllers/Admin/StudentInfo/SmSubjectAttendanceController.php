<?php

namespace App\Http\Controllers\Admin\StudentInfo;

use App\Http\Controllers\Admin\SystemSettings\SmSystemSettingController;
use App\Http\Requests\Admin\StudentInfo\StudentSubjectWiseAttendanceSearchRequest;
use App\Http\Requests\Admin\StudentInfo\StudentSubjectWiseAttendanceStoreRequest;
use App\Http\Requests\Admin\StudentInfo\StudentSubjectWiseAttendancSearchRequest;
use App\Http\Requests\Admin\StudentInfo\subjectAttendanceAverageReportSearchRequest;
use App\Imports\SubjectWiseAttendanceImport;
use App\Models\Shift;
use App\Models\SmAssignSubject;
use App\Models\SmBaseSetup;
use App\Models\SmClass;
use App\Models\SmClassSection;
use App\Models\SmNotification;
use App\Models\SmParent;
use App\Models\SmSection;
use App\Models\SmStudent;
use App\Models\SmStudentAttendance;
use App\Models\SmStudentCategory;
use App\Models\SmSubject;
use App\Models\SmSubjectAttendance;
use App\Models\StudentRecord;
use App\Traits\NotificationSend;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Branch\Entities\Branch;
use Modules\University\Entities\UnSubjectAssignStudent;
use Modules\University\Repositories\Interfaces\UnCommonRepositoryInterface;

class SmSubjectAttendanceController extends Controller
{
    use NotificationSend;

    public function index(Request $request)
    {
        /*
        try {
        */
        $classes = branchWise(SmClass::get());

        return view('backEnd.studentInformation.subject_attendance', compact('classes'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function search(StudentSubjectWiseAttendancSearchRequest $request)
    {
        /*
        try {
        */
        $data = [];
        $input['attendance_date'] = $request->attendance_date;
        $input['class'] = $request->class_id;
        $input['subject'] = $request->subject_id;
        $input['section'] = $request->section_id;
        $input['shift'] = shiftEnable() ? $request->shift : null;
        $input['branch_id'] = $request->branch_id ?? null;

        $classes = branchWise(SmClass::get());
        $sections = branchWise(SmClassSection::with('sectionName')->where('class_id', $input['class'])->get());
        $subjects = branchWise(SmAssignSubject::with('subject')->where('class_id', $input['class'])->where('section_id', $input['section'])
            ->when(shiftEnable(), function ($q) use ($input) {
                $q->where('shift_id', $input['shift']);
            })->get());

        if (moduleStatusCheck('University') === false) {
            request()->merge([
                'class' => $request->class_id,
                'section' => $request->section_id,
                'shift' => shiftEnable() ? $request->shift : null,
                'subject' => $request->subject_id,
            ]);
        }

        $students = StudentRecord::with('studentDetail', 'studentDetail.DateSubjectWiseAttendances')
            ->whereHas('studentDetail', function ($q) {
                $q->where('active_status', 1);
            })
            ->where('class_id', $input['class'])
            ->where('section_id', $input['section'])
            ->when(shiftEnable(), function ($q) use ($input) {
                $q->where('shift_id', $input['shift']);
            })
            ->when($request->branch_id, function ($q) use ($input) {
                branchWiseApplyFilter($q, 'branch_id', $input['branch_id']);
            })
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get();

        if (moduleStatusCheck('University')) {
            $data['un_semester_label_id'] = $request->un_semester_label_id;
            $interface = App::make(UnCommonRepositoryInterface::class);
            $data += $interface->searchInfo($request);
            $data += $interface->oldValueSelected($request);
            $assigned_students = UnSubjectAssignStudent::where('un_subject_id', $request->un_subject_id)
                ->where('un_semester_label_id', $request->un_semester_label_id)
                ->get('student_record_id')->toArray();
            $students = branchWise(StudentRecord::whereIn('id', $assigned_students)->get());
        }

        if ($students->isEmpty()) {
            Toastr::error('No Result Found', 'Failed');

            return redirect('subject-wise-attendance');
        }

        $attendance_type = $students[0]['studentDetail']['DateSubjectWiseAttendances'] !== null ? $students[0]['studentDetail']['DateSubjectWiseAttendances']['attendance_type'] : '';

        if (! moduleStatusCheck('University')) {
            $search_info['class_name'] = SmClass::find($request->class_id)?->class_name;
            $search_info['section_name'] = SmSection::find($request->section_id)?->section_name;
            $search_info['shift_name'] = shiftEnable() ? (Shift::find($request->shift)->name ?? '') : '';
            $search_info['subject_name'] = SmSubject::find($request->subject_id)->subject_name;
        }
        if (moduleStatusCheck('Branch') && $request->branch_id) {
            $search_info['branch_name'] = optional(Branch::find($request->branch_id))?->branch_name;
        }

        $search_info['date'] = $input['attendance_date'];

        $un_session_id = $request->un_session_id;
        $un_faculty_id = $request->un_faculty_id;
        $un_department_id = $request->un_department_id;
        $un_academic_id = $request->un_academic_id;
        $un_semester_id = $request->un_semester_id;
        $un_semester_label_id = $request->un_semester_label_id;
        $un_section_id = $request->un_section_id;
        $un_subject_id = $request->un_subject_id;

        $date_modified = \Carbon\Carbon::createFromFormat('m/d/Y', $search_info['date'])->format('Y-m-d');
        foreach ($students as $student) {
            $leave_request = \App\Models\SmLeaveRequest::where('staff_id', $student->student->user_id)
                ->where('approve_status', 'A')
                ->whereDate('leave_from', '<=', $date_modified)
                ->whereDate('leave_to', '>=', $date_modified)
                ->first();
            $student->is_on_leave = ! empty($leave_request);
            $student->leave_reason = $leave_request?->reason;
        }

        if (generalSetting()->attendance_layout === 1) {
            return view('backEnd.studentInformation.subject_attendance_list', compact('classes', 'subjects', 'sections', 'students', 'attendance_type', 'search_info', 'input', 'un_session_id', 'un_faculty_id', 'un_department_id', 'un_academic_id', 'un_semester_id', 'un_semester_label_id', 'un_section_id', 'un_subject_id'))->with($data);
        }

        return view('backEnd.studentInformation.subject_attendance_list2', compact('classes', 'subjects', 'sections', 'students', 'attendance_type', 'search_info', 'input', 'un_session_id', 'un_faculty_id', 'un_department_id', 'un_academic_id', 'un_semester_id', 'un_semester_label_id', 'un_section_id', 'un_subject_id'))->with($data);

        /*
        } catch (\Exception $e) {;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function storeAttendance(StudentSubjectWiseAttendanceStoreRequest $request)
    {

        if (moduleStatusCheck('University')) {
            $request->validate([
                'attendance_date' => 'required',
                'un_subject_id' => 'required',
                'attendance' => 'required|array',
                'attendance.*' => 'required',
                'date' => 'required',
            ]);
        } else {
            $request->validate([
                'class' => 'required',
                'section' => 'required',
                'subject' => 'required',
                'attendance_date' => 'required',
                'attendance' => 'required|array',
                'attendance.*' => 'required',
                'date' => 'required',
            ]);
        }

        try {

            foreach ($request->attendance as $record_id => $student) {
                $attendance = SmSubjectAttendance::where('student_id', gv($student, 'student'))
                    ->where('subject_id', $request->subject)
                    ->where('attendance_date', date('Y-m-d', strtotime($request->date)))
                    ->where('class_id', gv($student, 'class'))
                    ->where('section_id', gv($student, 'section'))
                    ->where('student_record_id', $record_id)
                    ->where('academic_id', getAcademicId())
                    ->when($request->branch_id, function ($q) use ($student) {
                        $q->where('branch_id', gv($student, 'branch_id'));
                    })
                    ->where('school_id', Auth::user()->school_id)
                    ->first();

                if ($attendance) {
                    $attendance->delete();
                }

                $attendance = new SmSubjectAttendance();
                $attendance->student_record_id = $record_id;
                $attendance->subject_id = $request->subject;
                $attendance->student_id = gv($student, 'student');
                $attendance->class_id = gv($student, 'class');
                $attendance->section_id = gv($student, 'section');
                if(moduleStatusCheck('Branch')){
                    $attendance->branch_id = gv($student, 'branch_id') ?? null;
                }

                $attendance->attendance_type = gv($student, 'attendance_type');
                $attendance->notes = gv($student, 'note');
                $attendance->school_id = Auth::user()->school_id;
                $attendance->academic_id = getAcademicId();
                $attendance->attendance_date = date('Y-m-d', strtotime($request->date));
                $r = $attendance->save();

                $student_user_id = SmStudent::find($attendance->student_id)->user_id;
                $data['class_id'] = $attendance->class_id;
                $data['section_id'] = $attendance->section_id;
                $data['subject'] = $attendance->subject->subject_name;
                $data['attendance_type'] = $attendance->attendance_type;
                $this->sent_notifications('Subject_Wise_Attendance', [$student_user_id], $data, ['Student', 'Parent']);

            }
            Toastr::success('Operation successful', 'Success');

            return redirect('subject-wise-attendance');

        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }

    }

    public function storeAttendanceSecond(Request $request)
    {

        /*
        try {
        */
        foreach ($request->attendance as $record_id => $student) {

            $attendance_type = gv($student, 'attendance_type') ? gv($student, 'attendance_type') : 'A';
            $attendance = SmSubjectAttendance::where('student_id', gv($student, 'student'))
                ->where('subject_id', $request->subject)
                ->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))
                ->where('class_id', gv($student, 'class'))
                ->where('section_id', gv($student, 'section'))
                ->where('student_record_id', $record_id)
                ->where('academic_id', getAcademicId())
                ->when($request->branch_id, function ($q) use ($student) {
                    $q->where('branch_id', gv($student, 'branch_id'));
                })
                ->where('school_id', Auth::user()->school_id)
                ->first();
            if ($attendance) {
                $attendance->delete();
            }

            $attendance = new SmSubjectAttendance();
            $attendance->student_record_id = $record_id;
            $attendance->subject_id = $request->subject;
            $attendance->student_id = gv($student, 'student');
            $attendance->class_id = gv($student, 'class');
            $attendance->section_id = gv($student, 'section');
            if(moduleStatusCheck('Branch')){
                $attendance->branch_id = gv($student, 'branch_id') ?? null;
            }
            $attendance->attendance_type = $attendance_type;
            $attendance->notes = gv($student, 'note');
            $attendance->school_id = Auth::user()->school_id;
            $attendance->academic_id = getAcademicId();
            $attendance->attendance_date = date('Y-m-d', strtotime($request->attendance_date));
            $r = $attendance->save();
        }

        return response()->json('success');
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function subjectHolidayStore(Request $request)
    {
        $active_students = SmStudent::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get()->pluck('id')->toArray();
        $students = branchWise(StudentRecord::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->when($request->branch_id, function ($q) use ($request) {
                branchWiseApplyFilter($q, 'branch_id', $request->branch_id);
            })
            ->whereIn('student_id', $active_students)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get());

        if ($students->isEmpty()) {
            Toastr::error('No Result Found', 'Failed');

            return redirect('subject-wise-attendance');
        }
        if ($request->purpose === 'mark') {
            foreach ($students as $record) {
                $attendance = SmSubjectAttendance::where('student_id', $record->student_id)
                    ->where('subject_id', $request->subject_id)
                    ->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))
                    ->where('class_id', $request->class_id)->where('section_id', $request->section_id)
                    ->when($request->branch_id, function ($q) use ($request) {
                        branchWiseApplyFilter($q, 'branch_id', $request->branch_id);
                    })
                    ->where('student_record_id', $record->id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->first();
                if (! empty($attendance)) {
                    $attendance->delete();
                    $attendance = new SmSubjectAttendance();
                    $attendance->attendance_type = 'H';
                    $attendance->notes = 'Holiday';
                    $attendance->attendance_date = date('Y-m-d', strtotime($request->attendance_date));
                    $attendance->student_id = $record->student_id;
                    $attendance->subject_id = $request->subject_id;
                    $attendance->student_record_id = $record->id;
                    $attendance->class_id = $record->class_id;
                    $attendance->section_id = $record->section_id;
                    if(moduleStatusCheck('Branch')){
                        $attendance->branch_id = $record->branch_id ?? null;
                    }

                    $attendance->academic_id = getAcademicId();
                    $attendance->school_id = Auth::user()->school_id;
                    $attendance->save();
                } else {
                    $attendance = new SmSubjectAttendance();
                    $attendance->attendance_type = 'H';
                    $attendance->notes = 'Holiday';
                    $attendance->attendance_date = date('Y-m-d', strtotime($request->attendance_date));
                    $attendance->student_id = $record->student_id;
                    $attendance->subject_id = $request->subject_id;

                    $attendance->student_record_id = $record->id;
                    $attendance->class_id = $record->class_id;
                    $attendance->section_id = $record->section_id;
                    if(moduleStatusCheck('Branch')){
                        $attendance->branch_id = $record->branch_id ?? null;
                    }


                    $attendance->academic_id = getAcademicId();
                    $attendance->school_id = Auth::user()->school_id;
                    $attendance->save();
                }

                // notification

                $messege = '';
                $date = dateConvert($attendance->attendance_date);

                $student = SmStudent::find($record->student_id);
                $subject = SmSubject::find($request->subject_id);
                $subject_name = $subject->subject_name;

                if ($student) {
                    $messege = app('translator')->get('student.Your_teacher_has_marked_holiday_in_the_attendance_on_subject', ['date' => $date, 'subject_name' => $subject_name]);

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

                    // for parent user
                    $parent = SmParent::find($student->parent_id);
                    if ($parent) {
                        $messege = app('translator')->get('student.Your_child_is_marked_holiday_in_the_attendance_on_subject', ['date' => $date, 'student_name' => $student->full_name."'s", 'subject_name' => $subject_name]);

                        $notification = new SmNotification();
                        $notification->user_id = $parent->user_id;
                        $notification->role_id = 3;
                        $notification->date = date('Y-m-d');
                        $notification->message = $messege;
                        $notification->school_id = Auth::user()->school_id;
                        $notification->academic_id = getAcademicId();
                        $notification->save();

                        try {
                            $user = User::find($notification->user_id);
                            if ($user) {
                                $title = app('translator')->get('student.attendance_notication');
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
                }
            }
        } elseif ($request->purpose === 'unmark') {
            foreach ($students as $record) {
                $attendance = SmSubjectAttendance::where('student_id', $record->student_id)
                    ->where('subject_id', $request->subject_id)
                    ->where('attendance_date', date('Y-m-d', strtotime($request->attendance_date)))
                    ->where('class_id', $request->class_id)->where('section_id', $request->section_id)
                    ->where('student_record_id', $record->id)
                    ->where('academic_id', getAcademicId())
                    ->where('school_id', Auth::user()->school_id)
                    ->first();
                if (! empty($attendance)) {
                    $attendance->delete();
                }
            }
        }
        Toastr::success('Operation successful', 'Success');

        return redirect('subject-wise-attendance');
    }

    public function subjectAttendanceReport(Request $request)
    {
        /*
        try {
        */

        $classes = branchWise(SmClass::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get());

        $types = branchWise(SmStudentCategory::where('school_id', Auth::user()->school_id)->get());

        $genders = branchWise(SmBaseSetup::where('active_status', '=', '1')
            ->where('base_group_id', '=', '1')
            ->where('school_id', Auth::user()->school_id)
            ->get());

        return view('backEnd.studentInformation.subject_attendance_report_view', compact('classes', 'types', 'genders'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function subjectAttendanceReportSearch(StudentSubjectWiseAttendanceSearchRequest $request)
    {

        /*
        try {
        */
        $year = $request->year;
        $month = $request->month;
        $class_id = $request->class;
        $section_id = $request->section;
        $branch_id = $request->branch_id ?? null;
        $shift_id = shiftEnable() ? $request->shift : null;

        $assign_subjects = SmAssignSubject::where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->when(shiftEnable(), function ($query) use ($shift_id) {
                $query->where('shift_id', $shift_id);
            })
            ->when($request->branch_id, function ($query) use ($branch_id) {
                branchWiseApplyFilter($query, 'branch_id', $branch_id);
            })
            ->first();

        if (! $assign_subjects) {
            Toastr::warning('Subject Not Assign', 'Failed');

            return redirect()->back();
        }
        $subject_id = $assign_subjects->subject_id;
        $current_day = date('d');

        $days = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);
        $classes = branchWise(SmClass::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get());

        $student_records = branchWise(StudentRecord::query());
        $student_records->where('school_id', auth()->user()->school_id)
            ->where('academic_id', getAcademicId())
            ->where('is_promote', 0)
            ->whereHas('student', function ($q) {
                $q->where('active_status', 1);
            });
        if ($class_id) {
            $student_records->where('class_id', $class_id);
        }
        if ($section_id) {
            $student_records->where('section_id', $section_id);
        }
        if ($shift_id) {
            $student_records->where('shift_id', $shift_id);
        }
        if ($branch_id) {
            branchWiseApplyFilter($student_records, 'branch_id', $branch_id);
        }
        $student_records = $student_records->get();
        $attendances = [];
        foreach ($student_records as $record) {
            $attendance = branchWise(SmSubjectAttendance::with('student')->where('student_record_id', $record->id)
                ->where('attendance_date', 'like', $year.'-'.$month.'%')
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get());

            if ($attendance) {
                $attendances[] = $attendance;
            }
        }

        return view('backEnd.studentInformation.subject_attendance_report_view', compact('classes', 'attendances', 'days', 'year', 'month', 'current_day', 'class_id', 'section_id', 'subject_id', 'shift_id', 'branch_id'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function subjectAttendanceAverageReport(Request $request)
    {

        /*
        try {
        */

        $classes = branchWise(SmClass::get());

        $types = branchWise(SmStudentCategory::withoutGlobalScope(AcademicSchoolScope::class)->where('school_id', Auth::user()->school_id)->get());

        $genders = branchWise(SmBaseSetup::where('base_group_id', '=', '1')->get());

        return view('backEnd.studentInformation.subject_attendance_report_average_view', compact('classes', 'types', 'genders'));
        /*
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function subjectAttendanceAverageReportSearch(subjectAttendanceAverageReportSearchRequest $request)
    {
        /*
        try {
        */

        $year = $request->year;

        $month = $request->month;

        $class_id = $request->class_id;

        $section_id = $request->section_id;

        $assign_subjects = SmAssignSubject::where('class_id', $class_id)->where('section_id', $section_id)->first();

        if (! $assign_subjects) {

            Toastr::error('No Subject Assign ', 'Failed');

            return redirect()->back();
        }
        $subject_id = $assign_subjects->subject_id;

        $current_day = date('d');

        $days = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);

        $classes = SmClass::get();
        $activeStudentIds = SmStudentAttendanceController::activeStudent()->pluck('id')->toArray();
        $students = StudentRecord::where('class_id', $request->class)->where('section_id', $request->section)->whereIn('student_id', $activeStudentIds)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get()->sortBy('roll_no');

        $attendances = [];

        foreach ($students as $record) {

            $attendance = SmSubjectAttendance::where('sm_subject_attendances.student_id', $record->student_id)

                //  ->join('student_records','student_records.student_id','=','sm_subject_attendances.student_id')

                // // ->where('subject_id', $subject_id)

                ->where('attendance_date', 'like', $year.'-'.$month.'%')
                ->where('sm_subject_attendances.student_record_id', $record->id)
                ->where('sm_subject_attendances.academic_id', getAcademicId())
                ->where('sm_subject_attendances.school_id', Auth::user()->school_id)

                ->get();

            if ($attendance) {

                $attendances[] = $attendance;
            }
        }
        $selected['class_id'] = $class_id;
        $selected['section_id'] = $section_id;

        //   return $attendances;
        return view('backEnd.studentInformation.subject_attendance_report_average_view', compact('classes', 'attendances', 'days', 'year', 'month', 'current_day', 'class_id', 'section_id', 'subject_id', 'selected'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function studentAttendanceReportPrint($class_id, $section_id, $month, $year)
    {
        /*
        try {
        */
        $current_day = date('d');
        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $classes = SmClass::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get();
        $activeStudentIds = SmStudentAttendanceController::activeStudent()->pluck('id')->toArray();
        $students = StudentRecord::where('class_id', $class_id)->where('section_id', $section_id)->whereIn('student_id', $activeStudentIds)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

        $attendances = [];
        foreach ($students as $record) {
            $attendance = SmStudentAttendance::where('student_id', $record->student_id)->where('attendance_date', 'like', $year.'-'.$month.'%')->where('school_id', Auth::user()->school_id)
                ->where('student_record_id', $record->id)
                ->get();
            if (count($attendance) !== 0) {
                $attendances[] = $attendance;
            }
        }

        return view('backEnd.studentInformation.student_attendance_report', compact('classes', 'attendances', 'days', 'year', 'month', 'current_day', 'class_id', 'section_id'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function subjectAttendanceReportAveragePrint($class_id, $section_id, $month, $year)
    {
        set_time_limit(2700);
        /*
        try {
        */
        $current_day = date('d');

        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $activeStudentIds = SmStudentAttendanceController::activeStudent()->pluck('id')->toArray();
        $students = StudentRecord::where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->whereIn('student_id', $activeStudentIds)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->get();

        $attendances = [];

        foreach ($students as $record) {
            $attendance = SmSubjectAttendance::where('sm_subject_attendances.student_id', $record->student_id)
                // ->join('student_records','student_records.student_id','=','sm_subject_attendances.student_id')
                ->where('sm_subject_attendances.student_record_id', $record->id)
                ->where('attendance_date', 'like', $year.'-'.$month.'%')
                ->where('sm_subject_attendances.academic_id', getAcademicId())
                ->where('sm_subject_attendances.school_id', Auth::user()->school_id)
                ->get();

            if ($attendance) {
                $attendances[] = $attendance;
            }
        }

        return view('backEnd.studentInformation.student_subject_attendance', compact('attendances', 'days', 'year', 'month', 'class_id', 'section_id'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function subjectAttendanceReportPrint($class_id, $section_id, $month, $year, $shift_id = null, $branch_id = null)
    {
        set_time_limit(2700);
        /*
        try {
        */
        $current_day = date('d');

        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $student_records = branchWise(StudentRecord::query());
        $student_records->where('school_id', auth()->user()->school_id)
            ->where('academic_id', getAcademicId())
            ->where('is_promote', 0)
            ->whereHas('student', function ($q) {
                $q->where('active_status', 1);
            });
        if ($class_id) {
            $student_records->where('class_id', $class_id);
        }
        if ($section_id) {
            $student_records->where('section_id', $section_id);
        }
        if ($shift_id) {
            $student_records->where('shift_id', $shift_id);
        }
        if ($branch_id) {
            branchWiseApplyFilter($student_records, 'branch_id', $branch_id);
        }
        $student_records = $student_records->get();
        $attendances = [];
        foreach ($student_records as $record) {
            $attendance = branchWise(SmSubjectAttendance::with('student')->where('student_record_id', $record->id)
                ->where('attendance_date', 'like', $year.'-'.$month.'%')
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get());

            if ($attendance) {
                $attendances[] = $attendance;
            }
        }
        $shift = null;
        if ($shift_id !== null) {
            $shift = Shift::find($shift_id);
        }

        $branch = null;
        if ($branch_id !== null) {
            $branch = Branch::find($branch_id);
        }

        return view('backEnd.studentInformation.student_subject_attendance', compact('attendances', 'days', 'year', 'month', 'class_id', 'section_id', 'shift', 'shift_id', 'branch'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function subjectWiseAttendanceImport()
    {

        /*
        try {
        */
        $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

        return view('backEnd.studentInformation.subject_wise_attendance_import', ['classes' => $classes]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function subjectWiseAttendanceBulkStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
            'class' => 'required',
            'section' => 'required',
            'subject_id' => 'required',
        ]);

        try {
            Excel::import(new SubjectWiseAttendanceImport(
                $request->class,
                $request->section,
                $request->subject_id
            ), $request->file('file'));

            Toastr::success('Attendance imported successfully.', 'Success');

            return redirect()->back();

        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }
}
