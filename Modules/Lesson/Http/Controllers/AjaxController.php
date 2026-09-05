<?php

namespace Modules\Lesson\Http\Controllers;

use App\Models\SmAssignSubject;
use App\Models\SmStaff;
use App\Models\SmSubject;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Lesson\Entities\SmLesson;
use Modules\Lesson\Entities\SmLessonTopicDetail;

class AjaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function ajaxSelectLesson(Request $request)
    {
        try {

            $lesson_all = branchWise(SmLesson::where('class_id', $request->class)
                ->where('section_id', '=', $request->section)
                ->where('subject_id', '=', $request->subject)
                ->get(['id', 'lesson_title']));

            $lessons = [];
            foreach ($lesson_all as $lesson) {
                $lessons[] = $lesson;
            }

            return response()->json([$lessons]);
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    // get topic from lesson
    public function ajaxSelectTopic(Request $request)
    {
        try {

            $topic_all = branchWise(SmLessonTopicDetail::where('lesson_id', $request->lesson_id)
                ->distinct('topic_id')
                ->get());
            $topics = [];
            foreach ($topic_all as $topic) {
                $topics[] = $topic;
            }

            return response()->json([$topics]);
        } catch (Exception $exception) {

            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function ajaxGetTopicRow(Request $request)
    {
        $topics = branchWise(SmLessonTopicDetail::where('lesson_id', $request->lesson_id)
            ->distinct('topic_id')
            ->get());

        return view('lesson::topic_row', ['topics' => $topics]);
    }

    // edit lesson plan
    public function getTopicRow($lessonPlanDetail)
    {
        return $topics = branchWise(SmLessonTopicDetail::where('lesson_id', $lessonPlanDetail->lesson_detail_id)
            ->distinct('topic_id')
            ->get());
    }

    public function getSubject(Request $request)
    {

        $class_id = $request->class;
        $selectedSections = $request->message_to_section;

        $subjectId = branchWise(SmSubject::query());
        $subjectId = $subjectId->where('class_id', $class_id);
        foreach ($selectedSections as $selectedSection) {
            $subjectId = $subjectId->where('section_id', $selectedSection);

        }

        return $subjectId->get();

    }

    public function getSubjectLesson(Request $request)
    {
        try {

            $staff_info = SmStaff::where('user_id', Auth::user()->id)->first();

            if (teacherAccess()) {
                $query = SmAssignSubject::where('class_id', '=', $request->class_id)
                    ->where('teacher_id', $staff_info->id);
            } else {
                $query = SmAssignSubject::where('class_id', '=', $request->class_id);
            }

            $subject_all = branchWise($query)->get();

            $students = [];
            $seen_ids = [];
            foreach ($subject_all as $allSubject) {
                if (in_array($allSubject->subject_id, $seen_ids)) {
                    continue;
                }
                $seen_ids[] = $allSubject->subject_id;
                $subject = SmSubject::find($allSubject->subject_id);
                if ($subject) {
                    $students[] = $subject;
                }
            }

            return response()->json([$students]);
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }
}
