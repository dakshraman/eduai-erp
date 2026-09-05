<?php

namespace App\Http\Controllers;

use App\Models\TeacherEvaluation;
use App\Models\TeacherEvaluationSetting;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TeacherEvaluationController extends Controller
{
    public function teacherEvaluationSetting()
    {
        $teacherEvaluationSetting = TeacherEvaluationSetting::where('id', 1)->first();

        return view('backEnd.teacherEvaluation.setting.teacherEvaluationSetting', ['teacherEvaluationSetting' => $teacherEvaluationSetting]);
    }

    public function teacherEvaluationSettingUpdate(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'endDate' => 'after:startDate',
        ]);
        if ($validator->fails()) {
            Toastr::error('End Date cannot be before Start Date', 'Failed');

            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $teacherEvaluationSetting = (TeacherEvaluationSetting::where("school_id", app("school")->id ?? 1)->first() ?: new TeacherEvaluationSetting());
            if ($request->type === 'evaluation') {
                $teacherEvaluationSetting->is_enable = $request->is_enable;
                $teacherEvaluationSetting->auto_approval = $request->auto_approval;
            }

            if ($request->type === 'submission') {
                $teacherEvaluationSetting->submitted_by = $request->submitted_by ?: $teacherEvaluationSetting->submitted_by;
                $teacherEvaluationSetting->rating_submission_time = $request->rating_submission_time;
                $teacherEvaluationSetting->from_date = date('Y-m-d', strtotime($request->startDate));
                $teacherEvaluationSetting->to_date = date('Y-m-d', strtotime($request->endDate));
            }

            $teacherEvaluationSetting->update();

            return redirect()->back();
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function teacherEvaluationSubmit(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'rating' => 'nullable|required_without:comment',
            'comment' => 'nullable|string|required_without:rating',
        ]);
        if ($validator->fails()) {
            Toastr::error('Empty Submission', 'Failed');

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $teacherEvaluationSetting = (TeacherEvaluationSetting::where("school_id", app("school")->id ?? 1)->first() ?: new TeacherEvaluationSetting());
            $teacherEvaluationQuery = TeacherEvaluation::query()
                ->where('record_id', $request->record_id)
                ->where('subject_id', $request->subject_id)
                ->where('teacher_id', $request->teacher_id)
                ->where('student_id', $request->student_id)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id);

            $teacherEvaluation = $teacherEvaluationQuery
                ->when($request->filled('evaluation_id'), function ($query) use ($request): void {
                    $query->whereKey($request->evaluation_id);
                })
                ->first() ?? new TeacherEvaluation();
            $teacherEvaluation->rating = $request->rating;
            $teacherEvaluation->comment = $request->comment;
            $teacherEvaluation->record_id = $request->record_id;
            $teacherEvaluation->subject_id = $request->subject_id;
            $teacherEvaluation->teacher_id = $request->teacher_id;
            $teacherEvaluation->student_id = $request->student_id;
            $teacherEvaluation->parent_id = $request->parent_id;
            $teacherEvaluation->role_id = Auth::user()->role_id;
            $teacherEvaluation->academic_id = getAcademicId();
            $teacherEvaluation->school_id = Auth::user()->school_id;
            if ($teacherEvaluationSetting->auto_approval === 0) {
                $teacherEvaluation->status = 1;
            }

            $teacherEvaluation->save();
            Toastr::success('Operation Successful', 'Success');

            return redirect()->back();
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }
}
