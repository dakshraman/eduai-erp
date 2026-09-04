<?php

namespace App\Http\Controllers\Admin\OnlineExam;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OnlineExam\SmQuestionGroupRequest;
use App\Models\SmQuestionGroup;
use App\Support\tableList;
use Illuminate\Support\Facades\Auth;

class SmQuestionGroupController extends Controller
{
    public function index()
    {
        /*
        try {
        */
        $groups = branchWise(SmQuestionGroup::get());

        return view('backEnd.examination.question_group', ['groups' => $groups]);
        /*
        } catch (Exception $exception) {
            toastrError();

            return redirect()->back();
        }
        */
    }

    public function store(SmQuestionGroupRequest $smQuestionGroupRequest)
    {
        /*
        try {
        */
        $smQuestionGroup = new SmQuestionGroup();
        $smQuestionGroup->title = $smQuestionGroupRequest->title;
        $smQuestionGroup->school_id = Auth::user()->school_id;
        $smQuestionGroup->academic_id = getAcademicId();
        if (moduleStatusCheck('Branch')) {
            $smQuestionGroup->branch_id = $smQuestionGroupRequest->branch_id;
        }
        $smQuestionGroup->save();
        toastrSuccess();

        return redirect()->back();
        /*
                } catch (Exception $exception) {
                    toastrError();

                    return redirect()->back();
                }
                */
    }

    public function show($id)
    {
        /*
        try {
        */
        $groups = branchWise(SmQuestionGroup::get());
        $group = $groups->firstWhere('id', $id);

        return view('backEnd.examination.question_group', ['groups' => $groups, 'group' => $group]);
        /*
        } catch (Exception $exception) {
            toastrError();

            return redirect()->back();
        }
        */
    }

    public function update(SmQuestionGroupRequest $smQuestionGroupRequest, $id)
    {
        /*
        try {
        */
        $group = SmQuestionGroup::find($smQuestionGroupRequest->id);
        $group->title = $smQuestionGroupRequest->title;
        if (moduleStatusCheck('Branch')) {
            $group->branch_id = $smQuestionGroupRequest->branch_id;
        }
        $group->save();

        toastrSuccess();

        return redirect('question-group');
        /*
        } catch (Exception $exception) {
            toastrError();

            return redirect()->back();
        }
        */
    }

    public function destroy($id)
    {
        $tables = tableList::getTableList('q_group_id', $id);

        /*
        try {
        */
        if ($tables === null || $tables== '') {
            SmQuestionGroup::destroy($id);

            toastrSuccess();

            return redirect('question-group');

        }

        $msg = 'This data already used in  : '.$tables.' Please remove those data first';

        toastrError($msg, 'Failed');

        return redirect()->back();

        /*
        } catch (Exception $exception) {
            $msg = 'This data already used in  : '.$tables.' Please remove those data first';
            toastrError($msg, 'Failed');

            return redirect()->back();
        }
        */
    }
}
