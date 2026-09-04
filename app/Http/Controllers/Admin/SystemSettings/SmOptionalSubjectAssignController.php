<?php

namespace App\Http\Controllers\Admin\SystemSettings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academics\AssignOptionalSubjectSearch;
use App\Http\Requests\Admin\GeneralSettings\SmOptionalSetupStoreRequest;
use App\Models\SmAssignSubject;
use App\Models\SmClass;
use App\Models\SmClassOptionalSubject;
use App\Models\SmClassSection;
use App\Models\SmOptionalSubjectAssign;
use App\Models\SmStaff;
use App\Models\SmSubject;
use App\Models\StudentRecord;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmOptionalSubjectAssignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function assignOptionalSubject(Request $request)
    {
        Toastr::error('Operation Failed', 'Failed');

        return redirect()->back();
    }

    public function index(Request $request)
    {

        $classes = branchWise(SmClass::get());
        $sections = branchWise(SmClassSection::get());
        $assign_subjects = branchWise(SmAssignSubject::get());
        $subjects = branchWise(SmSubject::get());
        $teachers = branchWise(SmStaff::where('role_id', 4)->get());

        return view('backEnd.academics.assign_optional_subject', ['classes' => $classes, 'sections' => $sections, 'assign_subjects' => $assign_subjects, 'subjects' => $subjects, 'teachers' => $teachers]);
    }

    public function assignOptionalSubjectSearch(AssignOptionalSubjectSearch $assignOptionalSubjectSearch)
    {
        /*
        try {
        */
        $students = branchWise(StudentRecord::with('studentDetail', 'studentDetail.subjectAssign', 'studentDetail.subjectAssign.subject')
            ->whereHas('studentDetail', function ($q) {
                return $q->where('active_status', 1);
            })
            ->where('class_id', $assignOptionalSubjectSearch->class_id)
            ->where('section_id', $assignOptionalSubjectSearch->section_id)
            ->when($assignOptionalSubjectSearch->shift, function ($q, $shift) {
                return $q->where('shift_id', $shift);
            })
            ->when($assignOptionalSubjectSearch->branch_id, function ($q, $branch_id) {
                return $q->where('branch_id', $branch_id);
            })
            ->where('academic_id', getAcademicId())
            ->where('is_promote', 0)
            ->where('school_id', Auth::user()->school_id)
            ->get());
        $subject_id = $assignOptionalSubjectSearch->subject_id;
        $subject_info = SmSubject::where('id', '=', $assignOptionalSubjectSearch->subject_id)->first();
        $subjects = branchWise(SmSubject::where('active_status', 1)->where('school_id', Auth::user()->school_id)->get());
        $teachers = branchWise(SmStaff::where('active_status', 1)->where(function ($q): void {
            $q->where('role_id', 4)->orWhere('previous_role_id', 4);
        })->where('school_id', Auth::user()->school_id)->get());

        $class_id = $assignOptionalSubjectSearch->class_id;
        $section_id = $assignOptionalSubjectSearch->section_id;
        $shift_id = $assignOptionalSubjectSearch->shift;
        $branch_id = $assignOptionalSubjectSearch->branch_id ?? null;
        $classes = branchWise(SmClass::get());
        $class = SmClass::with('classSection')->where('id', $class_id)->first();
        $assignSubjects = branchWise(SmAssignSubject::with('subject')
            ->where('class_id', $class_id)
            ->where('section_id', $section_id)
            ->when($shift_id, function ($query, $shift_id) {
                return $query->where('shift_id', $shift_id);
            })
            ->get());

        return view('backEnd.academics.assign_optional_subject', ['classes' => $classes, 'teachers' => $teachers, 'subjects' => $subjects, 'class_id' => $class_id, 'section_id' => $section_id, 'shift_id' => $shift_id, 'students' => $students, 'subject_id' => $subject_id, 'subject_info' => $subject_info, 'class' => $class, 'assignSubjects' => $assignSubjects, 'branch_id' => $branch_id]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function assignOptionalSubjectStore(Request $request)
    {
        /*
        try {
        */

        SmOptionalSubjectAssign::whereIn('record_id', $request->student_id)->delete();
        if ($request->student_id ) {
            foreach ($request->student_id as $student) {
                $student_info = StudentRecord::where('id', $student)->first();
                if ($student_info === null || $student_info->studentDetail === null) {
                    continue;
                }

                $optional_subject = new SmOptionalSubjectAssign();
                $optional_subject->student_id = $student_info->studentDetail->id;
                $optional_subject->record_id = $student;
                $optional_subject->subject_id = $request->subject_id;
                $optional_subject->session_id = $student_info->session_id;
                $optional_subject->created_by = Auth::user()->id;
                $optional_subject->school_id = Auth::user()->school_id;
                $optional_subject->academic_id = getAcademicId();
                if (moduleStatusCheck('Branch')) {
                    $optional_subject->branch_id = $student_info->branch_id;
                }
                $optional_subject->save();

            }
        } else {
            Toastr::warning('No Student Select', 'Warning');

            return redirect('optional-subject');
        }
        Toastr::success('Operation successful', 'Success');

        return redirect('optional-subject');

        /*
        } catch (Throwable $throwable) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect('optional-subject');
        }
        */
    }

    public function optionalSetup(Request $request)
    {

        /*
        try {
        */
        $classes = branchWise(SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get());
        $class_optionals = SmClassOptionalSubject::join('sm_classes', 'sm_classes.id', '=', 'sm_class_optional_subject.class_id')
            ->select('sm_class_optional_subject.*', 'class_name')
            ->where('sm_class_optional_subject.school_id', Auth::user()->school_id)
            ->where('sm_class_optional_subject.academic_id', getAcademicId())
            ->when(moduleStatusCheck('Branch') && userBranch(), function ($query): void {
                $query->where('sm_class_optional_subject.branch_id', userBranch());
            })
            ->orderby('sm_class_optional_subject.id', 'DESC')
            ->get();

        return view('backEnd.systemSettings.optional_subject_setup', ['classes' => $classes, 'class_optionals' => $class_optionals]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function optionalSetupStore(SmOptionalSetupStoreRequest $smOptionalSetupStoreRequest)
    {
        /*
        try {
        */
        $branch_id = moduleStatusCheck('Branch') ? ($smOptionalSetupStoreRequest->branch_id ?: userBranch()) : null;
        foreach ($smOptionalSetupStoreRequest->class as $value) {
            $optional_check = SmClassOptionalSubject::where('class_id', $value)
                ->where('school_id', Auth::user()->school_id)
                ->where('academic_id', getAcademicId())
                ->when(moduleStatusCheck('Branch'), function ($query) use ($branch_id): void {
                    $query->where('branch_id', $branch_id);
                })
                ->first();
            if ($optional_check === null) {
                $class_optional = new SmClassOptionalSubject();
                $class_optional->class_id = $value;
            } else {
                $class_optional = $optional_check;
            }
            $class_optional->gpa_above = $smOptionalSetupStoreRequest->gpa_above;
            $class_optional->school_id = Auth::user()->school_id;
            $class_optional->created_by = Auth::user()->id;
            $class_optional->updated_by = Auth::user()->id;
            $class_optional->academic_id = getAcademicId();
            if (moduleStatusCheck('Branch')) {
                $class_optional->branch_id = $branch_id;
            }
            $class_optional->save();
        }
        Toastr::success('Operation successful', 'Success');

        return redirect('optional-subject-setup');
        /*
        } catch (Throwable $throwable) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function optionalSetupDelete($id)
    {

        /*
        try {
        */
        $class_optional = SmClassOptionalSubject::findOrfail($id);
        $class_optional->delete();
        Toastr::success('Operation successful', 'Success');

        return redirect()->back();
        /*
        } catch (Throwable $throwable) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function optionalSetupEdit($id)
    {

        /*
        try {
        */
        $editData = SmClassOptionalSubject::findOrfail($id);
        $classes = branchWise(SmClass::where('active_status', 1)->where('school_id', Auth::user()->school_id)->where('sm_classes.academic_id', getAcademicId())->get());
        //    return $classes;
        $class_optionals = SmClassOptionalSubject::join('sm_classes', 'sm_classes.id', '=', 'sm_class_optional_subject.class_id')
            ->select('sm_class_optional_subject.*', 'class_name')
            ->where('sm_class_optional_subject.school_id', Auth::user()->school_id)
            ->where('sm_class_optional_subject.academic_id', getAcademicId())
            ->when(moduleStatusCheck('Branch') && userBranch(), function ($query): void {
                $query->where('sm_class_optional_subject.branch_id', userBranch());
            })
            ->get();

        return view('backEnd.systemSettings.optional_subject_setup', ['classes' => $classes, 'class_optionals' => $class_optionals, 'editData' => $editData]);
        /*
        } catch (Throwable $throwable) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }
}
