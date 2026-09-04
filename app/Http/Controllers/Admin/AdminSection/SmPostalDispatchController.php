<?php

namespace App\Http\Controllers\Admin\AdminSection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminSection\SmPostalDispatchRequest;
use App\Models\SmPostalDispatch;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmPostalDispatchController extends Controller
{
    public function index(Request $request)
    {
        /*
        try {
        */
        return view('backEnd.admin.postal_dispatch');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function store(SmPostalDispatchRequest $smPostalDispatchRequest)
    {
        /*
        try {
        */
        $destination = 'public/uploads/postal/';
        $fileName = fileUpload($smPostalDispatchRequest->image, $destination);
        $smPostalDispatch = new SmPostalDispatch();
        $smPostalDispatch->from_title = $smPostalDispatchRequest->from_title;
        $smPostalDispatch->reference_no = $smPostalDispatchRequest->reference_no;
        $smPostalDispatch->address = $smPostalDispatchRequest->address;
        $smPostalDispatch->date = date('Y-m-d', strtotime($smPostalDispatchRequest->date));
        $smPostalDispatch->note = $smPostalDispatchRequest->note;
        $smPostalDispatch->to_title = $smPostalDispatchRequest->to_title;
        $smPostalDispatch->file = $fileName;
        $smPostalDispatch->created_by = Auth::user()->id;
        $smPostalDispatch->school_id = Auth::user()->school_id;
        if (moduleStatusCheck('University')) {
            $smPostalDispatch->un_academic_id = getAcademicId();
        } else {
            $smPostalDispatch->academic_id = getAcademicId();
        }

        if (moduleStatusCheck('Branch')) {
            $smPostalDispatch->branch_id = $smPostalDispatchRequest->branch_id;
        }

        $smPostalDispatch->save();

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
        $postal_dispatchs = branchWise(SmPostalDispatch::get());
        $postal_dispatch = SmPostalDispatch::find($id);

        return view('backEnd.admin.postal_dispatch', ['postal_dispatchs' => $postal_dispatchs, 'postal_dispatch' => $postal_dispatch]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function update(SmPostalDispatchRequest $smPostalDispatchRequest)
    {
        /*
        try {
        */
        $destination = 'public/uploads/postal/';
        $postal_dispatch = SmPostalDispatch::find($smPostalDispatchRequest->id);

        $postal_dispatch->from_title = $smPostalDispatchRequest->from_title;
        $postal_dispatch->reference_no = $smPostalDispatchRequest->reference_no;
        $postal_dispatch->address = $smPostalDispatchRequest->address;
        $postal_dispatch->date = date('Y-m-d', strtotime($smPostalDispatchRequest->date));
        $postal_dispatch->note = $smPostalDispatchRequest->note;
        $postal_dispatch->to_title = $smPostalDispatchRequest->to_title;
        $postal_dispatch->file = fileUpdate($postal_dispatch->file, $smPostalDispatchRequest->file, $destination);
        if (moduleStatusCheck('University')) {
            $postal_dispatch->un_academic_id = getAcademicId();
        }

        if (moduleStatusCheck('Branch')) {
            $postal_dispatch->branch_id = $smPostalDispatchRequest->branch_id;
        }

        $postal_dispatch->save();

        Toastr::success('Operation successful', 'Success');

        return redirect('postal-dispatch');
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
        $postal_dispatch = SmPostalDispatch::find($request->id);
        if ($postal_dispatch->file  && file_exists($postal_dispatch->file)) {
            unlink($postal_dispatch->file);
        }

        $postal_dispatch->delete();

        Toastr::success('Operation successful', 'Success');

        return redirect('postal-dispatch');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function postalDispatchDatatable()
    {
        /*
        try {
        */
        $postal_dispatchs = branchWise(SmPostalDispatch::query());

        if (moduleStatusCheck('Branch')) {
            $postal_dispatchs->with('branch');
        }

        return DataTables::of($postal_dispatchs)
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
            ->addColumn('action', function ($row): string {

                $action = '';

                if (userPermission('postal-dispatch_edit')) {
                    $action .= '<a class="primary-btn small fix-gr-bg icon-only ml-10  l-h-30" href="'.route('postal-dispatch_edit', [$row->id]).'">
                                        <i class="fa fa-edit"></i>
                                    </a>';
                }

                if (userPermission('postal-dispatch-document') && ! empty($row->file) && file_exists($row->file)) {
                    $action .= '<a class="primary-btn small fix-gr-bg icon-only ml-10  l-h-30" href="'.url(@$row->file).'">
                                        <i class="fa fa-download"></i>
                                    </a>';
                }

                if (userPermission('postal-dispatch_delete')) {
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
