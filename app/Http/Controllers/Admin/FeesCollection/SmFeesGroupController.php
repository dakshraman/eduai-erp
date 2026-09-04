<?php

namespace App\Http\Controllers\Admin\FeesCollection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FeesCollection\SmFeesGroupRequest;
use App\Models\ApiBaseMethod;
use App\Models\SmFeesGroup;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmFeesGroupController extends Controller
{
    public function index(Request $request)
    {

        $fees_groups = branchWise(SmFeesGroup::get());

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($fees_groups, null);
        }

        return view('backEnd.feesCollection.fees_group', ['fees_groups' => $fees_groups]);

    }

    public function store(SmFeesGroupRequest $smFeesGroupRequest)
    {

        // if ($validator->fails()) {
        //     if (ApiBaseMethod::checkUrl($request->fullUrl())) {
        //         return ApiBaseMethod::sendError('Validation Error.', $validator->errors());
        //     }

        // }

        $smFeesGroup = new SmFeesGroup();
        $smFeesGroup->name = $smFeesGroupRequest->name;
        $smFeesGroup->description = $smFeesGroupRequest->description;
        $smFeesGroup->school_id = Auth::user()->school_id;
        if (moduleStatusCheck('University')) {
            $smFeesGroup->un_academic_id = getAcademicId();
        } else {
            $smFeesGroup->academic_id = getAcademicId();
        }
        if (moduleStatusCheck('Branch')) {
            $smFeesGroup->branch_id = $smFeesGroupRequest->branch_id;
        }

        $result = $smFeesGroup->save();

        if (ApiBaseMethod::checkUrl($smFeesGroupRequest->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Fees Group has been created successfully.');
            }

            return ApiBaseMethod::sendError('Something went wrong, please try again.');

        }

        Toastr::success('Operation successful', 'Success');

        return redirect()->back();

    }

    public function edit(Request $request, $id)
    {

        $fees_group = SmFeesGroup::find($id);
        $fees_groups = branchWise(SmFeesGroup::where('school_id', Auth::user()->school_id)->where('academic_id', getAcademicId())->get());

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            $data = [];
            $data['fees_group'] = $fees_group->toArray();
            $data['fees_groups'] = $fees_groups->toArray();

            return ApiBaseMethod::sendResponse($data, null);
        }

        return view('backEnd.feesCollection.fees_group', ['fees_group' => $fees_group, 'fees_groups' => $fees_groups]);

    }

    public function update(SmFeesGroupRequest $smFeesGroupRequest)
    {

        $fees_group = SmFeesGroup::find($smFeesGroupRequest->id);
        $fees_group->name = $smFeesGroupRequest->name;
        $fees_group->description = $smFeesGroupRequest->description;
        if (moduleStatusCheck('University')) {
            $fees_group->un_academic_id = getAcademicId();
        } else {
            $fees_group->academic_id = getAcademicId();
        }
        if (moduleStatusCheck('Branch')) {
            $fees_group->branch_id = $smFeesGroupRequest->branch_id;
        }
        $result = $fees_group->save();

        if (ApiBaseMethod::checkUrl($smFeesGroupRequest->fullUrl())) {
            if ($result) {
                return ApiBaseMethod::sendResponse(null, 'Fees Group has been updated successfully.');
            }

            return ApiBaseMethod::sendError('Something went wrong, please try again.');

        }

        Toastr::success('Operation successful', 'Success');

        return redirect('fees-group');

    }

    public function deleteGroup(Request $request)
    {

        $tables = \App\Support\tableList::getTableList('fees_group_id', $request->id);
        if ($tables === null || $tables== '') {

            $fees_group = SmFeesGroup::destroy($request->id);

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                if ($fees_group) {
                    return ApiBaseMethod::sendResponse(null, 'Fees Group has been deleted successfully');
                }

                return ApiBaseMethod::sendError('Something went wrong, please try again');

            }

            Toastr::success('Operation successful', 'Success');

            return redirect()->back();

        }

        $msg = 'This data already used in  : '.$tables.' Please remove those data first';
        Toastr::error($msg, 'Failed');

        return redirect()->back();

    }
}
