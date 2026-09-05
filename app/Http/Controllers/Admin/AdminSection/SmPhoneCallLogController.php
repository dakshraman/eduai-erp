<?php

namespace App\Http\Controllers\Admin\AdminSection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminSection\SmPhoneCallRequest;
use App\Models\SmPhoneCallLog;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class SmPhoneCallLogController extends Controller
{
    public function index(Request $request)
    {
        /*
        try {
        */
        return view('backEnd.admin.phone_call');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function store(SmPhoneCallRequest $smPhoneCallRequest)
    {
        /*
        try {
        */
        $smPhoneCallLog = new SmPhoneCallLog();
        $smPhoneCallLog->name = $smPhoneCallRequest->name;
        $smPhoneCallLog->phone = $smPhoneCallRequest->phone;
        $smPhoneCallLog->date = date('Y-m-d', strtotime($smPhoneCallRequest->date));
        $smPhoneCallLog->description = $smPhoneCallRequest->description;
        $smPhoneCallLog->next_follow_up_date = date('Y-m-d', strtotime($smPhoneCallRequest->follow_up_date));
        $smPhoneCallLog->call_duration = $smPhoneCallRequest->call_duration;
        $smPhoneCallLog->call_type = $smPhoneCallRequest->call_type;
        $smPhoneCallLog->active_status = 1;
        $smPhoneCallLog->school_id = Auth::user()->school_id;
        if (moduleStatusCheck('University') && Schema::hasColumn('sm_phone_call_logs', 'un_academic_id')) {
            $smPhoneCallLog->un_academic_id = getAcademicId();
        } else {
            $smPhoneCallLog->academic_id = getAcademicId();
        }
        if (moduleStatusCheck('Branch')) {
            $smPhoneCallLog->branch_id = $smPhoneCallRequest->branch_id;
        }
        $smPhoneCallLog->save();

        Toastr::success('Operation successful', 'Success');

        return redirect()->back();
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function show(Request $request, $id)
    {
        /*
        try {
        */
        $phone_call_logs = branchWise(SmPhoneCallLog::get());
        $phone_call_log = SmPhoneCallLog::find($id);

        return view('backEnd.admin.phone_call', ['phone_call_logs' => $phone_call_logs, 'phone_call_log' => $phone_call_log]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function update(SmPhoneCallRequest $smPhoneCallRequest, $id)
    {
        /*
        try {
        */
        $phone_call_log = SmPhoneCallLog::find($smPhoneCallRequest->id);
        $phone_call_log->name = $smPhoneCallRequest->name;
        $phone_call_log->phone = $smPhoneCallRequest->phone;
        $phone_call_log->date = date('Y-m-d', strtotime($smPhoneCallRequest->date));
        $phone_call_log->description = $smPhoneCallRequest->description;
        $phone_call_log->next_follow_up_date = date('Y-m-d', strtotime($smPhoneCallRequest->follow_up_date));
        $phone_call_log->call_duration = $smPhoneCallRequest->call_duration;
        $phone_call_log->call_type = $smPhoneCallRequest->call_type;
        if (moduleStatusCheck('University')) {
            $phone_call_log->un_academic_id = getAcademicId();
        }
        if (moduleStatusCheck('Branch')) {
            $phone_call_log->branch_id = $smPhoneCallRequest->branch_id;
        }

        $phone_call_log->save();

        Toastr::success('Operation successful', 'Success');

        return redirect('phone-call');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function destroy(Request $request)
    {
        /*
        try {
        */
        $phone_call_log = SmPhoneCallLog::find($request->id);
        $result = $phone_call_log->delete();

        Toastr::success('Operation successful', 'Success');

        return redirect('phone-call');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function phoneCallDatatable()
    {
        /*
        try {
        */
        $phone_call_logs = branchWise(SmPhoneCallLog::query());

        if (moduleStatusCheck('Branch')) {
            $phone_call_logs = $phone_call_logs->with('branch');
        }

        return DataTables::of($phone_call_logs)
            ->addIndexColumn()
            ->addColumn('query_date', function ($row) {
                return dateConvert(@$row->date);
            })
            ->addColumn('branch', function ($row) {
                if (moduleStatusCheck('Branch')) {
                    return optional($row->branch)->branch_name;
                }else{
                    return  '';
                }
            })
            ->addColumn('next_follow_up_date', function ($row) {
                return dateConvert(@$row->next_follow_up_date);
            })
            ->addColumn('call_type', function ($row) {
                return __('admin.'.($row->call_type === 'I' ? 'incoming' : 'outgoing'));
            })
            ->addColumn('action', function ($row): string {
                $action = '';

                if (userPermission('phone-call_edit')) {
                    $action .= '<a class="primary-btn small fix-gr-bg icon-only ml-10  l-h-30" href="'.route('phone-call_edit', [$row->id]).'">
                                        <i class="fa fa-edit"></i>
                                    </a>';
                }
                if (userPermission('phone-call_delete')) {
                    $action .= ' <a class="primary-btn small fix-gr-bg icon-only ml-10 l-h-30" href="javascript:void(0)" onclick="deleteQueryModal('.$row->id.')" >
                                        <i class="fa fa-trash"></i>
                                    </a>';
                }

                return $action;
            })
            ->rawColumns(['action', 'date'])
            ->make(true);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }
}
