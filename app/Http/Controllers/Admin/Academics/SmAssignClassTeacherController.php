<?php

namespace App\Http\Controllers\Admin\Academics;

use App\Events\ClassTeacherGetAllStudent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Academics\SmAssignClassTeacherRequest;
use App\Models\SmAssignClassTeacher;
use App\Models\SmClass;
use App\Models\SmClassTeacher;
use App\Models\SmSection;
use App\Models\SmStaff;
use App\Traits\NotificationSend;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SmAssignClassTeacherController extends Controller
{
    use NotificationSend;

    public function index(Request $request)
    {

        $classes = branchWise(SmClass::get());
        $teachers = branchWise(SmStaff::status()->where(function ($q): void {
            $q->where('role_id', 4)->orWhere('previous_role_id', 4);
        })->get());
        $assign_class_teachers = branchWise(SmAssignClassTeacher::with('class', 'section', 'classTeachers')->where('academic_id', getAcademicId())->status()->orderBy('class_id', 'ASC')->orderBy('section_id', 'ASC')->get());

        return view('backEnd.academics.assign_class_teacher', ['classes' => $classes, 'teachers' => $teachers, 'assign_class_teachers' => $assign_class_teachers]);

    }

    public function store(SmAssignClassTeacherRequest $smAssignClassTeacherRequest)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();

            $query = SmAssignClassTeacher::where('active_status', 1)
                ->where('class_id', $smAssignClassTeacherRequest->class)
                ->where('section_id', $smAssignClassTeacherRequest->section)
                ->where('academic_id', getAcademicId())
                ->where('school_id', $user->school_id);

            if (shiftEnable()) {
                $query->where('shift_id', $smAssignClassTeacherRequest->shift);
            }
            if (moduleStatusCheck('Branch')) {
                $query->where('branch_id', $smAssignClassTeacherRequest->branch_id);
            }

            $assigned_class_teacher = $query->first();

            if (empty($assigned_class_teacher)) {
                $smAssignClassTeacher = new SmAssignClassTeacher();
                $smAssignClassTeacher->class_id = $smAssignClassTeacherRequest->class;
                $smAssignClassTeacher->section_id = $smAssignClassTeacherRequest->section;
                $smAssignClassTeacher->school_id = $user->school_id;
                if (moduleStatusCheck('Branch')) {
                    $smAssignClassTeacher->branch_id = $smAssignClassTeacherRequest->branch_id;
                }
                $smAssignClassTeacher->academic_id = getAcademicId();

                if (shiftEnable()) {
                    $smAssignClassTeacher->shift_id = $smAssignClassTeacherRequest->shift;
                }

                $smAssignClassTeacher->save();

                $smClassTeacher = new SmClassTeacher();
                $smClassTeacher->assign_class_teacher_id = $smAssignClassTeacher->id;
                $smClassTeacher->teacher_id = $smAssignClassTeacherRequest->teacher;
                $smClassTeacher->school_id = $user->school_id;
                if (moduleStatusCheck('Branch')) {
                    $smClassTeacher->branch_id = $smAssignClassTeacherRequest->branch_id;
                }
                $smClassTeacher->academic_id = getAcademicId();
                $smClassTeacher->save();

                event(new ClassTeacherGetAllStudent($smAssignClassTeacher, $smClassTeacher));
                DB::commit();

                $data['class_id'] = $smAssignClassTeacherRequest->class;
                $data['section_id'] = $smAssignClassTeacherRequest->section;
                $data['teacher_name'] = $smClassTeacher->teacher->full_name;
                if (shiftEnable()) {
                    $data['shift_id'] = $smAssignClassTeacherRequest->shift;
                }

                $this->sent_notifications('Assign_Class_Teacher', (array) $smClassTeacher->teacher->user_id, $data, ['Teacher']);

                $records = $this->studentRecordInfo(
                    $smAssignClassTeacherRequest->class,
                    $smAssignClassTeacherRequest->section,
                    shiftEnable() ? $smAssignClassTeacherRequest->shift : null
                )->pluck('studentDetail.user_id');

                $this->sent_notifications('Assign_Class_Teacher', $records, $data, ['Student', 'Parent']);
                Toastr::success('Operation successful', 'Success');

                return redirect()->back();
            }
            DB::rollBack();
            Toastr::warning('Class Teacher already assigned.', 'Warning');

            return redirect()->back();

        } catch (Exception $exception) {
            DB::rollBack();
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }

    }

    public function edit(Request $request, $id)
    {

        $classes = branchWise(SmClass::get());
        $teachers = branchWise(SmStaff::status()->where(function ($q): void {
            $q->where('role_id', 4)->orWhere('previous_role_id', 4);
        })->get());
        $assign_class_teachers = branchWise(SmAssignClassTeacher::with('class', 'section', 'classTeachers')->where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get());
        $assign_class_teacher = SmAssignClassTeacher::find($id);
        if ($assign_class_teacher === null) {
            Toastr::error('Record not found', 'Failed');

            return redirect()->back();
        }

        $sections = branchWise(SmSection::get());
        $teacherId = [];
        if ($assign_class_teacher->classTeachers) {
            foreach ($assign_class_teacher->classTeachers as $classTeacher) {
                $teacherId[] = $classTeacher->teacher_id;
            }
        }

        return view('backEnd.academics.assign_class_teacher', ['assign_class_teacher' => $assign_class_teacher, 'classes' => $classes, 'teachers' => $teachers, 'assign_class_teachers' => $assign_class_teachers, 'sections' => $sections, 'teacherId' => $teacherId]);

    }

    public function update(SmAssignClassTeacherRequest $smAssignClassTeacherRequest, $id)
    {
        $user = Auth::user();
        $is_duplicate = SmAssignClassTeacher::where('school_id', $user->school_id)->where('academic_id', getAcademicId())->where('class_id', $smAssignClassTeacherRequest->class)->where('section_id', $smAssignClassTeacherRequest->section)->where('id', '!=', $smAssignClassTeacherRequest->id)->first();
        if ($is_duplicate) {
            Toastr::warning('Duplicate entry found!', 'Warning');

            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            SmClassTeacher::where('assign_class_teacher_id', $smAssignClassTeacherRequest->id)->delete();

            $assign_class_teacher = SmAssignClassTeacher::find($smAssignClassTeacherRequest->id);
            if ($assign_class_teacher === null) {
                DB::rollBack();
                Toastr::error('Record not found', 'Failed');

                return redirect()->back();
            }

            $assign_class_teacher->class_id = $smAssignClassTeacherRequest->class;
            $assign_class_teacher->academic_id = getAcademicId();
            $assign_class_teacher->section_id = $smAssignClassTeacherRequest->section;
            if (shiftEnable()) {
                $assign_class_teacher->shift_id = $smAssignClassTeacherRequest->shift;
            } else {
                $assign_class_teacher->shift_id = null;
            }
            if (moduleStatusCheck('Branch')) {
                $assign_class_teacher->branch_id = $smAssignClassTeacherRequest->branch_id;
            }
            $assign_class_teacher->save();
            $assign_class_teacher_collection = $assign_class_teacher;
            $assign_class_teacher->toArray();

            $smClassTeacher = new SmClassTeacher();
            $smClassTeacher->assign_class_teacher_id = $assign_class_teacher->id;
            $smClassTeacher->teacher_id = $smAssignClassTeacherRequest->teacher;
            $smClassTeacher->school_id = $user->school_id;
            $smClassTeacher->academic_id = getAcademicId();
            if (moduleStatusCheck('Branch')) {
                $smClassTeacher->branch_id = $smAssignClassTeacherRequest->branch_id;
            }
            $smClassTeacher->save();

            event(new ClassTeacherGetAllStudent($assign_class_teacher_collection, $smClassTeacher, 'update'));
            DB::commit();
            Toastr::success('Operation successful', 'Success');

            return redirect('assign-class-teacher');
        } catch (Exception $exception) {
            DB::rollBack();
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $id_key = 'assign_class_teacher_id';
            $tables = \App\Support\tableList::getTableList($id_key, $id);

            try {
                DB::beginTransaction();
                SmClassTeacher::where('assign_class_teacher_id', $id)->delete();
                SmAssignClassTeacher::destroy($id);
                DB::commit();
                Toastr::success('Operation successful', 'Success');

                return redirect()->back();
            } catch (\Illuminate\Database\QueryException $e) {
                DB::rollback();
                $msg = 'This data already used in  : '.$tables.' Please remove those data first';
                Toastr::error($msg, 'Failed');

                return redirect()->back();
            }
        } catch (Exception $exception) {
            DB::rollback();
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }
}
