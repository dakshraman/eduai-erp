<?php

namespace Modules\DownloadCenter\Http\Controllers;

use App\Models\SmClass;
use App\Models\SmSection;
use App\Models\SmStudent;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\DownloadCenter\Entities\VideoUpload;

class VideoUploadController extends Controller
{
    public function videoList()
    {

        try {
            $user = auth()->user();
            if ($user->role_id === 2) {
                $student = SmStudent::where('user_id', $user->id)->with('studentRecord')->first();
                if (moduleStatusCheck('University')) {
                    $videos = branchWise(VideoUpload::where('un_session_id', $student->studentRecord->un_session_id)
                        ->where('un_faculty_id', $student->studentRecord->un_faculty_id)
                        ->where('un_department_id', $student->studentRecord->un_department_id)
                        ->where('un_academic_id', $student->studentRecord->un_academic_id)
                        ->where('un_semester_id', $student->studentRecord->un_semester_id)
                        ->where('un_semester_label_id', $student->studentRecord->un_semester_label_id)
                        ->with('unDepartment', 'unSession', 'unFaculty', 'unSemester', 'unSemesterLabel')
                        ->get());
                } else {
                    $videos = branchWise(VideoUpload::where('class_id', $student->studentRecord->class_id)
                        ->where('section_id', $student->studentRecord->section_id)
                        ->with('class', 'section', 'user')
                        ->get());
                }

            } else {
                $videos = branchWise(VideoUpload::with('class', 'section', 'user')->get());
            }
            $classes = branchWise(SmClass::get());

            return view('downloadcenter::videoUpload.videoList', compact('classes', 'videos'));
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function videoListSave(Request $request)
    {

        $input = $request->all();

        if (moduleStatusCheck('University')) {
            $validator = Validator::make($input, [
                'un_session_id' => 'required',
                'un_faculty_id' => 'required',
                'un_department_id' => 'required',
                'un_academic_id' => 'required',
                'un_semester_id' => 'required',
                'un_semester_label_id' => 'required',
                'title' => 'required',
                'video_link' => 'required',
            ], [
                'un_session_id' => 'The Session field is required.',
                'un_faculty_id' => 'The Faculty field is required.',
                'un_department_id' => 'The Department field is required.',
                'un_academic_id' => 'The Academic Year field is required.',
                'un_semester_id' => 'The Semester field is required.',
                'un_semester_label_id' => 'The Semester label field is required.',
            ]);
        } else {

            $validator = Validator::make($input, [
                'class_id' => 'required',
                'section_id' => 'required',
                'title' => 'required',
                'video_link' => 'required',
            ], [
                'class_id' => 'The class field is required.',
                'section_id' => 'The section field is required.',
            ]);
        }

        if ($validator->fails()) {
            Toastr::error('Please fill all the required fields', 'Failed');

            return redirect()->back()->withErrors($validator)->withInput();
        }
        if (youtubeVideoLinkValidation($request->video_link) === 0) {
            Toastr::error('Only YouTube Video link accepted', 'Failed');

            return redirect()->back();
        }

        try {
            $newContent = new VideoUpload();

            $newContent->title = $request->title;
            $newContent->description = $request->description;

            if (moduleStatusCheck('University')) {
                $newContent->un_session_id = $request->un_session_id;
                $newContent->un_faculty_id = $request->un_faculty_id;
                $newContent->un_department_id = $request->un_department_id;
                $newContent->un_academic_id = $request->un_academic_id;
                $newContent->un_semester_id = $request->un_semester_id;
                $newContent->un_semester_label_id = $request->un_semester_label_id;
            } else {
                $newContent->class_id = $request->class_id;
                $newContent->section_id = $request->section_id;
                $newContent->shift_id = shiftEnable() ? $request->shift : '';
            }
            if (moduleStatusCheck('Branch')) {
                $newContent->branch_id = $request->branch_id;
            }

            $newContent->youtube_link = $request->video_link;
            $newContent->created_by = auth()->user()->id;
            $newContent->save();

            Toastr::success('Operation Successful', 'Success');

            return redirect()->route('download-center.video-list');
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function videoListUpdate(Request $request)
    {
        $input = $request->all();
        if (moduleStatusCheck('University')) {
            $validator = Validator::make($input, [
                'un_session_id' => 'required',
                'un_faculty_id' => 'required',
                'un_department_id' => 'required',
                'un_academic_id' => 'required',
                'un_semester_id' => 'required',
                'un_semester_label_id' => 'required',
                'title' => 'required',
                'video_link' => 'required',
            ], [
                'un_session_id' => 'The Session field is required.',
                'un_faculty_id' => 'The Faculty field is required.',
                'un_department_id' => 'The Department field is required.',
                'un_academic_id' => 'The Academic Year field is required.',
                'un_semester_id' => 'The Semester field is required.',
                'un_semester_label_id' => 'The Semester label field is required.',
            ]);
        } else {

            $validator = Validator::make($input, [
                'class_id' => 'required',
                'section_id' => 'required',
                'title' => 'required',
                'video_link' => 'required',
            ], [
                'class_id' => 'The class field is required.',
                'section_id' => 'The section field is required.',
            ]);
        }
        if ($validator->fails()) {
            Toastr::error('Please fill all the required fields', 'Failed');

            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (youtubeVideoLinkValidation($request->video_link) === 0) {
            Toastr::error('Only YouTube link accepted', 'Failed');

            return redirect()->back();
        }

        try {
            $editContent = VideoUpload::find($request->video_id);
            if (moduleStatusCheck('University')) {
                $editContent->un_session_id = $request->un_session_id;
                $editContent->un_faculty_id = $request->un_faculty_id;
                $editContent->un_department_id = $request->un_department_id;
                $editContent->un_academic_id = $request->un_academic_id;
                $editContent->un_semester_id = $request->un_semester_id;
                $editContent->un_semester_label_id = $request->un_semester_label_id;
            } else {
                $editContent->class_id = $request->class_id;
                $editContent->section_id = $request->section_id;
                $editContent->shift_id = shiftEnable() ? $request->shift : '';
            }
            $editContent->title = $request->title;
            $editContent->description = $request->description;
            $editContent->youtube_link = $request->video_link;
            if (moduleStatusCheck('Branch')) {
                $editContent->branch_id = $request->branch_id;
            }
            $editContent->save();

            Toastr::success('Operation Successful', 'Success');

            return redirect()->route('download-center.video-list');
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function videoListDelete($id)
    {
        try {
            $content = VideoUpload::where('id', $id)->first();
            $content->delete();
            Toastr::success('Deleted successfully', 'Success');

            return redirect()->route('download-center.video-list');
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function videoListSearch(Request $request)
    {
        try {
            if (moduleStatusCheck('University')) {
                $videos = VideoUpload::when($request->un_session_id, function ($q) use ($request) {
                    $q->where('un_session_id', $request->un_session_id);
                })
                    ->when($request->un_faculty_id, function ($q) use ($request) {
                        $q->where('un_faculty_id', $request->un_faculty_id);
                    })
                    ->when($request->un_department_id, function ($q) use ($request) {
                        $q->where('un_department_id', $request->un_department_id);
                    })
                    ->when($request->un_academic_id, function ($q) use ($request) {
                        $q->where('un_academic_id', $request->un_academic_id);
                    })
                    ->when($request->un_semester_id, function ($q) use ($request) {
                        $q->where('un_semester_id', $request->un_semester_id);
                    })
                    ->when($request->un_semester_label_id, function ($q) use ($request) {
                        $q->where('un_semester_label_id', $request->un_semester_label_id);
                    })
                    ->when($request->branch_id, function ($q) use ($request) {
                        $q->where('branch_id', $request->branch_id);
                    })
                    ->get();
            } else {
                $videos = VideoUpload::when($request->class, function ($q) use ($request) {
                    $q->where('class_id', $request->class);
                })
                    ->when($request->section, function ($q) use ($request) {
                        $q->where('section_id', $request->section);
                    })
                    ->when($request->shift, function ($q) use ($request) {
                        $q->where('shift_id', $request->shift);
                    })
                    ->when($request->branch_id, function ($q) use ($request) {
                        $q->where('branch_id', $request->branch_id);
                    })
                    ->when($request->title, function ($q) use ($request) {
                        $q->where('title', 'LIKE', '%'.$request->title.'%');
                    })->get();
            }
            $class_id = $request->class;
            $section_id = $request->section;
            $shift_id = $request->shift;
            $title = $request->title;
            $branch_id = $request->branch_id ?? '';
            $classes = branchWise(SmClass::get());

            $un_session_id = $request->un_session_id;
            $un_faculty_id = $request->un_faculty_id;
            $un_department_id = $request->un_department_id;
            $un_academic_id = $request->un_academic_id;
            $un_semester_id = $request->un_semester_id;
            $un_semester_label_id = $request->un_semester_label_id;
            $un_section_id = $request->un_section_id;

            if ($videos->isEmpty()) {
                Toastr::error('No data found', 'Failed');

                return redirect()->back()->withInput();
            }

            return view('downloadcenter::videoUpload.videoList', [
                'classes' => $classes,
                'videos' => $videos,
                'class_id' => $class_id,
                'section_id' => $section_id,
                'shift_id' => $shift_id,
                'title' => $title,
                'un_session_id' => $un_session_id,
                'un_faculty_id' => $un_faculty_id,
                'un_department_id' => $un_department_id,
                'un_academic_id' => $un_academic_id,
                'un_semester_id' => $un_semester_id,
                'un_semester_label_id' => $un_semester_label_id,
                'un_section_id' => $un_section_id,
                'branch_id' => $branch_id,
            ]);

        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back()->withInput();
        }
    }

    public function videoListViewModal($id)
    {
        try {
            $video = VideoUpload::with('class', 'section', 'user')->find($id);

            return view('downloadcenter::videoUpload.video_view_modal', compact('video'));
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function videoListEditModal($id)
    {
        try {
            $data['video'] = VideoUpload::with('class', 'section', 'user')->find($id);
            $data['classes'] = branchWise(SmClass::get());
            $data['sections'] = branchWise(SmSection::get());

            return view('downloadcenter::videoUpload.video_edit_modal', $data);
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function parentVideoList($id)
    {
        try {
            $student_detail = SmStudent::where('id', $id)->with('studentRecord')->first();
            $records = branchWise(studentRecords(null, $student_detail->id)->get());
            $videos = branchWise(VideoUpload::where('class_id', $student_detail->studentRecord->class_id)
                ->where('section_id', $student_detail->studentRecord->section_id)
                ->with('class', 'section', 'user')
                ->get());
            $classes = branchWise(SmClass::get());

            return view('downloadcenter::videoUpload.parentVideoList', compact('classes', 'videos', 'student_detail', 'records'));
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }
}
