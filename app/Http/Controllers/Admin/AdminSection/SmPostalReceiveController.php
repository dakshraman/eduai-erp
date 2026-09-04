<?php

namespace App\Http\Controllers\Admin\AdminSection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminSection\SmPostalReceiveRequest;
use App\Models\SmPostalReceive;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmPostalReceiveController extends Controller
{
    public function index(Request $request)
    {
        /*
        try {
        */
        return view('backEnd.admin.postal_receive');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function store(SmPostalReceiveRequest $request)
    {
        if ($request->hasFile('file')) {
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            $supportedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'txt'];
            if (! in_array($fileExtension, $supportedExtensions)) {
                Toastr::error('Unsupported File', 'Failed');

                return redirect()->back();
            }
        }

        /*        try { */
        $destination = 'public/uploads/postal/';
        $fileName = fileUpload($request->file, $destination);
        $smPostalReceive = new SmPostalReceive();
        $smPostalReceive->from_title = $request->from_title;
        $smPostalReceive->reference_no = $request->reference_no;
        $smPostalReceive->address = $request->address;
        $smPostalReceive->date = date('Y-m-d', strtotime($request->date));
        $smPostalReceive->note = $request->note;
        $smPostalReceive->to_title = $request->to_title;
        $smPostalReceive->file = $fileName;
        $smPostalReceive->created_by = Auth::user()->id;
        $smPostalReceive->school_id = Auth::user()->school_id;
        if (moduleStatusCheck('University')) {
            $smPostalReceive->un_academic_id = getAcademicId();
        } else {
            $smPostalReceive->academic_id = getAcademicId();
        }

        if (moduleStatusCheck('Branch')) {
            $smPostalReceive->branch_id = $request->branch_id;
        }

        $smPostalReceive->save();

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
        $postal_receives = branchWise(SmPostalReceive::get());
        $postal_receive = SmPostalReceive::find($id);

        return view('backEnd.admin.postal_receive', ['postal_receives' => $postal_receives, 'postal_receive' => $postal_receive]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function update(SmPostalReceiveRequest $smPostalReceiveRequest)
    {
        /*
        try {
        */
        $destination = 'public/uploads/postal/';
        $postal_receive = SmPostalReceive::find($smPostalReceiveRequest->id);
        $postal_receive->from_title = $smPostalReceiveRequest->from_title;
        $postal_receive->reference_no = $smPostalReceiveRequest->reference_no;
        $postal_receive->address = $smPostalReceiveRequest->address;
        $postal_receive->date = date('Y-m-d', strtotime($smPostalReceiveRequest->date));
        $postal_receive->note = $smPostalReceiveRequest->note;
        $postal_receive->to_title = $smPostalReceiveRequest->to_title;
        $postal_receive->file = fileUpdate($postal_receive->file, $smPostalReceiveRequest->file, $destination);
        if (moduleStatusCheck('University')) {
            $postal_receive->un_academic_id = getAcademicId();
        }
        if (moduleStatusCheck('Branch')) {
            $postal_receive->branch_id = $smPostalReceiveRequest->branch_id;
        }

        $postal_receive->save();

        Toastr::success('Operation successful', 'Success');

        return redirect('postal-receive');
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
        $postal_receive = SmPostalReceive::find($request->id);
        if ($postal_receive->file  && file_exists($postal_receive->file)) {
            unlink($postal_receive->file);
        }

        $postal_receive->delete();

        Toastr::success('Operation successful', 'Success');

        return redirect('postal-receive');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function postalReceiveDatatable()
    {
        /*
        try {
        */
        $postal_receives = branchWise(SmPostalReceive::query());
        $postal_receives->select('sm_postal_receives.*');
        
        if (moduleStatusCheck('Branch')) {
            $postal_receives->with('branch');
        }

        return DataTables::of($postal_receives)
            ->addIndexColumn()
            ->addColumn('query_date', function ($row) {
                return dateConvert(@$row->date);
            })
            ->addColumn('address', function ($row) {
                return $row->address;
            })
            ->addColumn('branch', function ($row) {
                if (moduleStatusCheck('Branch')) {
                    return optional($row->branch)->branch_name;
                }else{
                    return  '';
                }
            })
            ->addColumn('action', function ($row): string {

                $action = '';

                if (userPermission('postal-receive_edit')) {
                    $action .= '<a class="primary-btn small fix-gr-bg icon-only ml-10  l-h-30" href="'.route('postal-receive_edit', [$row->id]).'">
                                        <i class="fa fa-edit"></i>
                                    </a>';
                }

                if (userPermission('postal-receive-document') && ! empty($row->file)) {
                    $action .= '<a class="primary-btn small fix-gr-bg icon-only ml-10  l-h-30" href="'.url(@$row->file).'">
                                        <i class="fa fa-download"></i>
                                    </a>';
                }

                if (userPermission('postal-receive_delete')) {
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
