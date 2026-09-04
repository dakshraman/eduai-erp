<?php

namespace App\Http\Controllers\Admin\AdminSection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminSection\SmVisitorRequest;
use App\Models\SmVisitor;
use App\Traits\NotificationSend;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class SmVisitorController extends Controller
{
    use NotificationSend;

    public function index(Request $request)
    {
        /*
        try {
        */
        return view('backEnd.admin.visitor');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function store(SmVisitorRequest $smVisitorRequest)
    {
        /*
        try {
        */
        $destination = 'public/uploads/visitor/';
        $fileName = fileUpload($smVisitorRequest->upload_event_image, $destination);
        $smVisitor = new SmVisitor();
        $smVisitor->name = $smVisitorRequest->name;
        $smVisitor->phone = $smVisitorRequest->phone;
        $smVisitor->visitor_id = $smVisitorRequest->visitor_id;
        $smVisitor->no_of_person = $smVisitorRequest->no_of_person;
        $smVisitor->purpose = $smVisitorRequest->purpose;
        $smVisitor->date = date('Y-m-d', strtotime($smVisitorRequest->date));
        $smVisitor->in_time = $smVisitorRequest->in_time;
        $smVisitor->out_time = $smVisitorRequest->out_time;
        $smVisitor->file = $fileName;
        $smVisitor->created_by = auth()->user()->id;
        if (moduleStatusCheck('Branch')) {
            $smVisitor->branch_id = $smVisitorRequest->branch_id;
        }
        $smVisitor->school_id = Auth::user()->school_id;
        if (moduleStatusCheck('University')) {
            $smVisitor->un_academic_id = getAcademicId();
        } else {
            $smVisitor->academic_id = getAcademicId();
        }

        $smVisitor->save();

        Toastr::success('Operation successful', 'Success');

        return redirect()->back();
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function edit(Request $request, $id)
    {
        /*
        try {
        */
        $visitor = SmVisitor::find($id);
        $visitors = branchWise(SmVisitor::orderby('id', 'DESC')->get());

        return view('backEnd.admin.visitor', ['visitor' => $visitor, 'visitors' => $visitors]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function update(SmVisitorRequest $smVisitorRequest)
    {
        /*
        try {
        */
        $destination = 'public/uploads/visitor/';
        $visitor = SmVisitor::find($smVisitorRequest->id);
        $visitor->name = $smVisitorRequest->name;
        $visitor->phone = $smVisitorRequest->phone;
        $visitor->visitor_id = $smVisitorRequest->visitor_id;
        $visitor->no_of_person = $smVisitorRequest->no_of_person;
        $visitor->purpose = $smVisitorRequest->purpose;
        $visitor->date = date('Y-m-d', strtotime($smVisitorRequest->date));
        $visitor->in_time = $smVisitorRequest->in_time;
        $visitor->out_time = $smVisitorRequest->out_time;
        $visitor->file = fileUpdate($visitor->file, $smVisitorRequest->upload_event_image, $destination);
        if (moduleStatusCheck('University')) {
            $visitor->un_academic_id = getAcademicId();
        }
        if (moduleStatusCheck('Branch')) {
            $visitor->branch_id = $smVisitorRequest->branch_id;
        }
        $visitor->save();
        Toastr::success('Operation successful', 'Success');

        return redirect('visitor');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function delete(Request $request)
    {
        /*
        try {
        */
        $visitor = SmVisitor::find($request->id);
        if ($visitor->file  && file_exists($visitor->file)) {
            unlink($visitor->file);
        }

        $visitor->delete();

        Toastr::success('Operation successful', 'Success');

        return redirect('visitor');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function download_files($id)
    {
        /*
        try {
        */
        $visitor = SmVisitor::find($id);
        if (file_exists($visitor->file)) {
            return Response::download($visitor->file);
        }
        /*
        } catch (Throwable $throwable) {
            Toastr::error('File not found', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function visitorDatatable()
    {
        /*
        try {
        */

        if (moduleStatusCheck('Branch')) {
            $visitors = branchWise(SmVisitor::query()->with(['user', 'branch']));
        } else {
            $visitors = branchWise(SmVisitor::query()->with(['user']));
        }

        return DataTables::of($visitors)
            ->addIndexColumn()
            ->addColumn('query_date', function ($row) {
                return dateConvert(@$row->date);
            })
            ->addColumn('branch', function ($row) {
                if(moduleStatusCheck('Branch')){
                    return @$row->branch->branch_name;
                }else{
                    return  '';
                }
            })
            ->addColumn('created_by', function ($row) {
                return @$row->created_by === null ? 'Visitor' : $row->user->full_name;
            })
            ->addColumn('action', function ($row): string {
                $action = '';

                if (userPermission('visitor_edit')) {
                    $action = '<a class="primary-btn small fix-gr-bg icon-only ml-10  l-h-30" href="'.route('visitor_edit', [$row->id]).'">
                                        <i class="fa fa-edit"></i>
                                    </a>';

                }

                if (userPermission('visitor_download')) {
                    $action .= '<a class="primary-btn small fix-gr-bg icon-only ml-10  l-h-30" href="'.route('visitor_download', [$row->id]).'">
                                        <i class="fa fa-download"></i>
                                    </a>';
                }

                if (userPermission('admission_query_delete')) {
                    $action .= ' <a class="primary-btn small fix-gr-bg icon-only ml-10 l-h-30" href="javascript:void(0)" onclick="deleteQueryModal('.$row->id.')" >
                                        <i class="fa fa-trash"></i>
                                    </a>';
                }

                return $action;

            })->rawColumns(['action', 'date'])
            ->make(true);
        /*
        } catch (Throwable $throwable) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }
}
