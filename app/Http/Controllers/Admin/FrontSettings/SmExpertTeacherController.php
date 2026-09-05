<?php

namespace App\Http\Controllers\Admin\FrontSettings;

use App\Http\Controllers\Controller;
use App\Models\SmExpertTeacher;
use App\Models\SmStaff;
use App\Support\GlobalVariable;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Modules\RolePermission\Entities\InfixRole;

class SmExpertTeacherController extends Controller
{
    public function index()
    {
        /*
        try {
        */
        $expertTeachers = SmExpertTeacher::where('school_id', auth()->user()->school_id)->orderBy('position', 'asc')->with('staff.designations')->get();
        $roles = InfixRole::where('is_saas', 0)->where('active_status', '=', '1')
            ->whereNotIn('id', [1, 2, 3, GlobalVariable::isAlumni()])
            ->where(function ($q): void {
                $q->where('school_id', auth()->user()->school_id)->orWhere('type', 'System');
            })->get();

        return view('backEnd.frontSettings.expert_teacher.expert_teacher', ['expertTeachers' => $expertTeachers, 'roles' => $roles]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function store(Request $request)
    {
        /*
        try {
        */
        $request->validate([
            'member_type' => 'required',
            'staff' => 'required',
        ]);

        $sm_staff = SmStaff::where('school_id', auth()->user()->school_id)
            ->where(function ($query) use ($request): void {
                $query->where('user_id', $request->staff)
                    ->orWhere('id', $request->staff);
            })
            ->first();

        if ($sm_staff === null) {
            Toastr::error('Staff not found', 'Failed');

            return redirect()->route('expert-teacher');
        }

        $staffExists = SmExpertTeacher::where('staff_id', $sm_staff->id)
            ->where('school_id', auth()->user()->school_id)
            ->first();
        if ($staffExists === null) {
            $smExpertTeacher = new SmExpertTeacher();
            $smExpertTeacher->staff_id = $sm_staff->id;
            $smExpertTeacher->created_by = auth()->user()->id;
            $smExpertTeacher->school_id = auth()->user()->school_id;
            if (moduleStatusCheck('Branch')) {
                $smExpertTeacher->branch_id = $sm_staff->branch_id;
            }
            $smExpertTeacher->save();

            if ($sm_staff !== null) {
                $sm_staff->show_public = 1;
                $sm_staff->update();
            }

            Toastr::success('Operation successful', 'Success');

            return redirect()->route('expert-teacher');
        }

        Toastr::error('Already Set As Expert Staff', 'Failed');

        return redirect()->route('expert-teacher');

        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    // public function edit($id)
    // {
    //     /*
    //     try {
    //     */
    //         $expertTeacher = SmExpertTeacher::find($id);

    //         $roles = InfixRole::where('is_saas', 0)->where('active_status', '=', '1')
    //             ->whereNotIn('id', [1, 2, 3, GlobalVariable::isAlumni()])
    //             ->where(function ($q): void {
    //                 $q->where('school_id', auth()->user()->school_id)->orWhere('type', 'System');
    //             })->get();
    //         $expertTeachers = SmExpertTeacher::where('school_id', auth()->user()->school_id)->orderBy('position', 'asc')->with('staff.designations')->get();
    //         return view('backEnd.frontSettings.expert_teacher.expert_teacher', ['expertTeacher' => $expertTeacher, 'roles' => $roles, 'expertTeachers' => $expertTeachers]);
    //     /*
    //     } catch (Exception $exception) {
    //         Toastr::error('Operation Failed', 'Failed');

    //         return redirect()->back();
    //     }
    //     */
    // }

    public function deleteModal($id)
    {
        /*
        try {
        */
        $expertTeacher = SmExpertTeacher::find($id);

        return view('backEnd.frontSettings.expert_teacher.expert_teacher_delete_modal', ['expertTeacher' => $expertTeacher]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function delete($id)
    {
        /*
        try {
        */
        $expertTeacher = SmExpertTeacher::where('id', $id)->first();

        if ($expertTeacher === null) {
            Toastr::error('Expert staff not found', 'Failed');

            return redirect()->back();
        }

        $staff = SmStaff::find($expertTeacher->staff_id);
        if ($staff !== null) {
            $staff->show_public = 0;
            $staff->update();
        }

        $expertTeacher->delete();
        Toastr::success('Deleted successfully', 'Success');

        return redirect()->back();
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }
}
