<?php

namespace App\Http\Controllers\Admin\Academics;

use App\Events\CreateClassGroupChat;
use App\Http\Controllers\Controller;
use App\Imports\BulkImport;
use App\Models\ApiBaseMethod;
use App\Models\Shift;
use App\Models\SmAssignSubject;
use App\Models\SmClass;
use App\Models\SmClassSection;
use App\Models\SmSection;
use App\Models\SmStaff;
use App\Models\SmSubject;
use App\Support\YearCheck;
use App\Traits\NotificationSend;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Modules\Branch\Entities\Branch;
use Modules\University\Entities\UnSubject;

class SmAssignSubjectController extends Controller
{
    use NotificationSend;

    public function index(Request $request)
    {

        /*
        try {
        */
        $classes = branchWise(SmClass::get());
        if ($request->filled('class') && $request->filled('section')) {
            $assign_subjects = $this->assignSubjectListQuery($request)->get();
            $subjects = $this->assignableSubjects();
            $teachers = $this->assignableTeachers();
            $class_id = $request->class;
            $section_id = $request->section;
            $shift_id = shiftEnable() ? $request->shift : null;
            $branch_id = $this->selectedBranchId($request);

            return view('backEnd.academics.assign_subject', compact('classes', 'assign_subjects', 'teachers', 'subjects', 'class_id', 'section_id', 'shift_id', 'branch_id'));
        }

        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($classes, null);
        }

        return view('backEnd.academics.assign_subject', ['classes' => $classes]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function create(Request $request)
    {
        /*
        try {
        */
        $classes = branchWise(SmClass::get());
        if (ApiBaseMethod::checkUrl($request->fullUrl())) {
            return ApiBaseMethod::sendResponse($classes, null);
        }

        return view('backEnd.academics.assign_subject_create', ['classes' => $classes]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function ajaxSubjectDropdown(Request $request)
    {
        /*
        try {
        */
        $staff_info = SmStaff::where('user_id', Auth::user()->id)->first();
        if (teacherAccess()) {
            $class_id = $request->class;
            $allSubjects = branchWise(SmAssignSubject::where([['section_id', '=', $request->id], ['class_id', $class_id], ['teacher_id', $staff_info->id]])->where('school_id', Auth::user()->school_id)->get());
            $subjectsName = [];
            foreach ($allSubjects as $allSubject) {
                $subjectsName[] = SmSubject::find($allSubject->subject_id);
            }
        } else {
            $class_id = $request->class;
            $allSubjects = branchWise(SmAssignSubject::where([['section_id', '=', $request->id], ['class_id', $class_id]])->where('school_id', Auth::user()->school_id)->get());

            $subjectsName = [];
            foreach ($allSubjects as $allSubject) {
                $subjectsName[] = SmSubject::find($allSubject->subject_id);
            }
        }

        return response()->json([$subjectsName]);
        /*
        } catch (Exception $exception) {
            return Response::json(['error' => 'Error msg'], 404);
        }
        */
    }

    public function search(Request $request)
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
                'un_section_id' => 'required',
            ]);
        } else {
            $validator = Validator::make($input, [
                'class' => 'required',
                'section' => 'required',
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        /*
        try {
        */

        if (! moduleStatusCheck('University')) {
            $branchId = $this->selectedBranchId($request);
            $sections = $this->applyAssignSubjectBranchFilter(SmClassSection::where('class_id', $request->class)
                ->with('sectionName', 'className')
                ->when($request->section, function ($q) use ($request): void {
                    $q->where('section_id', $request->section);
                }), $branchId)->get();

            $assign_subjects = $this->assignSubjectListQuery($request)->get();
            $subjects = $this->assignableSubjects();
            $teachers = $this->assignableTeachers();

            $class_id = $request->class;
            $section_id = $request->section;
            $shift_id = shiftEnable() ? $request->shift : null;
            $branch_id = $branchId;

            $classes = branchWise(SmClass::where('active_status', 1)
                ->where('academic_id', getAcademicId())
                ->where('school_id', Auth::user()->school_id)
                ->get());

            return view('backEnd.academics.assign_subject_create', [
                'classes' => $classes,
                'sections' => $sections,
                'assign_subjects' => $assign_subjects,
                'teachers' => $teachers,
                'subjects' => $subjects,
                'class_id' => $class_id,
                'section_id' => $section_id,
                'shift_id' => $shift_id,
                'branch_id' => $branch_id,
            ]);
        }
        $teachers = branchWise(SmStaff::where('active_status', 1)
            ->where(function ($q): void {
                $q->where('role_id', 4)->orWhere('previous_role_id', 4);
            })->where('school_id', Auth::user()->school_id)->get());

        $subjects = UnSubject::where('un_department_id', $input['un_department_id'])
            ->where('un_faculty_id', $input['un_faculty_id'])
            ->where('school_id', Auth::user()->school_id)
            ->get();
        $assign_subjects = branchWise(SmAssignSubject::where('un_faculty_id', $request->un_department_id)
            ->where('un_department_id', $request->un_department_id)
            ->where('un_section_id', $request->un_section_id)
            ->where('un_session_id', $request->un_session_id)
            ->where('un_semester_label_id', $request->un_semester_label_id)
            ->where('un_academic_id', $request->un_academic_id)
            ->where('school_id', Auth::user()->school_id)
            ->get());

        return view('backEnd.academics.assign_subject_create', ['assign_subjects' => $assign_subjects, 'teachers' => $teachers, 'subjects' => $subjects, 'un_input' => $input]);

        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function assignSubjectAjax(Request $request)
    {

        /*
        try {
        */
        $subjects = branchWise(SmSubject::get());
        $teachers = branchWise(SmStaff::status()->where(function ($q): void {
            $q->where('role_id', 4)->orWhere('previous_role_id', 4);
        })->get());

        return response()->json([$subjects, $teachers]);
        /*
        } catch (Exception $exception) {
            return Response::json(['error' => 'Error msg'], 404);
        }
        */
    }

    public function assignSubjectStore(Request $request)
    {

        // try{
        $user = Auth::user();
        if ($request->subjects && $request->teachers && is_null($request->subjects[0]) && is_null($request->teachers[0])) {
            Toastr::warning('Empty data submit', 'warning');

            return redirect()->back();
        }
        if ($request->update == 0) {
            $i = 0;
            if ($request->subjects) {
                foreach ($request->subjects as $key => $subject) {
                    if ($subject) {
                        if (moduleStatusCheck('University')) {
                            $assign_subject = new SmAssignSubject();
                            $assign_subject->school_id = $user->school_id;
                            $assign_subject->un_faculty_id = $request->un_faculty_id;
                            $assign_subject->un_department_id = $request->un_department_id;
                            $assign_subject->un_section_id = $request->un_section_id;
                            $assign_subject->un_session_id = $request->un_session_id;
                            $assign_subject->un_semester_label_id = $request->un_semester_label_id;
                            $assign_subject->un_academic_id = $request->un_academic_id;
                            $assign_subject->un_subject_id = $subject;
                            $assign_subject->teacher_id = $request->teachers[$i];
                            $assign_subject->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                            $assign_subject->academic_id = getAcademicId();
                            if (moduleStatusCheck('Branch')) {
                                $assign_subject->branch_id = $request->branch_id;
                            }
                            $assign_subject->save();
                            $i++;
                        } else {
                            if (! $request->section) {
                                $all_section = SmClassSection::where('class_id', $request->class)->get();
                                foreach ($all_section as $section) {
                                    $branchId = $this->selectedBranchId($request);
                                    $assign_subject = new SmAssignSubject();
                                    $assign_subject->class_id = $request->class;
                                    $assign_subject->school_id = $user->school_id;
                                    $assign_subject->section_id = $section->section_id;
                                    $assign_subject->shift_id = shiftEnable() ? $request->shift : null;
                                    $assign_subject->subject_id = $subject;
                                    $assign_subject->teacher_id = $request->teachers[$key];
                                    $assign_subject->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                                    $assign_subject->academic_id = getAcademicId();
                                    if (moduleStatusCheck('Branch')) {
                                        $assign_subject->branch_id = $branchId;
                                    }
                                    $assign_subject->save();
                                    event(new CreateClassGroupChat($assign_subject));
                                }
                            } else {
                                $branchId = $this->selectedBranchId($request);
                                $assign_subject = new SmAssignSubject();
                                $assign_subject->class_id = $request->class;
                                $assign_subject->school_id = $user->school_id;
                                $assign_subject->section_id = $request->section;
                                $assign_subject->shift_id = shiftEnable() ? $request->shift : null;
                                $assign_subject->subject_id = $subject;
                                $assign_subject->teacher_id = $request->teachers[$i];
                                $assign_subject->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                                $assign_subject->academic_id = getAcademicId();
                                if (moduleStatusCheck('Branch')) {
                                    $assign_subject->branch_id = $branchId;
                                }
                                $assign_subject->save();
                                event(new CreateClassGroupChat($assign_subject));
                                $i++;
                            }
                        }
                    }
                }
            }
        } elseif ($request->update == 1) {
            if (moduleStatusCheck('University')) {
                $i = 0;
                if ($request->subjects) {
                    foreach ($request->subjects as $key => $subject) {
                        SmAssignSubject::where('un_faculty_id', $request->un_faculty_id)
                            ->where('un_department_id', $request->un_department_id)
                            ->where('un_section_id', $request->un_section_id)
                            ->where('un_session_id', $request->un_session_id)
                            ->where('un_semester_label_id', $request->un_semester_label_id)
                            ->where('un_academic_id', $request->un_academic_id)
                            ->where('un_subject_id', $subject)
                            ->delete();
                        if ($subject) {
                            $assign_subject = new SmAssignSubject();
                            $assign_subject->school_id = $user->school_id;
                            $assign_subject->un_faculty_id = $request->un_faculty_id;
                            $assign_subject->un_department_id = $request->un_department_id;
                            $assign_subject->un_section_id = $request->un_section_id;
                            $assign_subject->un_session_id = $request->un_session_id;
                            $assign_subject->un_semester_label_id = $request->un_semester_label_id;
                            $assign_subject->un_academic_id = $request->un_academic_id;
                            $assign_subject->un_subject_id = $subject;
                            $assign_subject->teacher_id = $request->teachers[$i];
                            $assign_subject->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                            $assign_subject->academic_id = getAcademicId();
                            if (moduleStatusCheck('Branch')) {
                                $assign_subject->branch_id = $request->branch_id;
                            }
                            $assign_subject->save();
                        }
                    }
                }
            } else {

                if (! $request->section) {
                    if ($request->subjects) {
                        foreach ($request->subjects as $key => $subject) {
                            if ($subject) {
                                $all_section = SmClassSection::where('class_id', $request->class)->get();
                                foreach ($all_section as $section) {
                                    $branchId = $this->selectedBranchId($request);
                                    $assign_subject = new SmAssignSubject();
                                    $assign_subject->class_id = $request->class;
                                    $assign_subject->section_id = $section->section_id;
                                    $assign_subject->shift_id = shiftEnable() ? $request->shift : null;
                                    $assign_subject->subject_id = $subject;
                                    $assign_subject->teacher_id = $request->teachers[$key];
                                    $assign_subject->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                                    $assign_subject->academic_id = getAcademicId();
                                    $assign_subject->school_id = $user->school_id;
                                    if (moduleStatusCheck('Branch')) {
                                        $assign_subject->branch_id = $branchId;
                                    }
                                    $assign_subject->save();
                                    event(new CreateClassGroupChat($assign_subject));
                                }
                            }
                        }
                    }
                } else {
                    $this->assignSubjectListQuery($request)->delete();
                    $i = 0;
                    if ($request->subjects) {
                        foreach ($request->subjects as $subject) {
                            if ($subject) {
                                $branchId = $this->selectedBranchId($request);
                                $assign_subject = new SmAssignSubject();
                                $assign_subject->class_id = $request->class;
                                $assign_subject->section_id = $request->section;
                                $assign_subject->shift_id = shiftEnable() ? $request->shift : null;
                                $assign_subject->subject_id = $subject;
                                $assign_subject->teacher_id = $request->teachers[$i];
                                $assign_subject->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                                $assign_subject->academic_id = getAcademicId();
                                $assign_subject->school_id = $user->school_id;
                                if (moduleStatusCheck('Branch')) {
                                    $assign_subject->branch_id = $branchId;
                                }
                                $assign_subject->save();
                                event(new CreateClassGroupChat($assign_subject));
                                $i++;
                            }
                        }
                    }
                }
            }
        }
        Toastr::success('Operation successful', 'Success');

        if (! moduleStatusCheck('University')) {
            return redirect()->route('assign_subject', [
                'branch_id' => $this->selectedBranchId($request),
                'shift' => $request->shift,
                'class' => $request->class,
                'section' => $request->section,
            ]);
        }

        return redirect()->back();
        // }catch(Exception $e){
        // }
    }

    public function assignSubjectFind(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $classes = branchWise(SmClass::get());
        $assign_subjects = $this->assignSubjectListQuery($request)->get();
        $subjects = $this->assignableSubjects();
        $teachers = $this->assignableTeachers();

        if ($assign_subjects->count() === 0) {
            Toastr::error('No Result Found', 'Failed');

            return redirect()->back();
        }

        $class_id = $request->class;
        $section_id = $request->section;
        $shift_id = $request->shift;
        $branch_id = $this->selectedBranchId($request);

        return view('backEnd.academics.assign_subject', ['classes' => $classes, 'assign_subjects' => $assign_subjects, 'teachers' => $teachers, 'subjects' => $subjects, 'class_id' => $class_id, 'section_id' => $section_id, 'shift_id' => $shift_id, 'branch_id' => $branch_id]);
    }

    private function assignSubjectListQuery(Request $request)
    {
        $query = SmAssignSubject::where('class_id', $request->class)
            ->where('school_id', Auth::user()->school_id)
            ->where('academic_id', getAcademicId())
            ->when($request->section, function ($query) use ($request): void {
                $query->where('section_id', $request->section);
            })
            ->when(shiftEnable() && $request->shift, function ($query) use ($request): void {
                $query->where('shift_id', $request->shift);
            });

        return $this->applyAssignSubjectBranchFilter($query, $this->selectedBranchId($request));
    }

    private function assignableSubjects()
    {
        return branchWise(SmSubject::where('active_status', 1)
            ->where('school_id', Auth::user()->school_id)
            ->where('academic_id', getAcademicId())
            ->get());
    }

    private function assignableTeachers()
    {
        return branchWise(SmStaff::where('active_status', 1)
            ->where(function ($query): void {
                $query->where('role_id', 4)->orWhere('previous_role_id', 4);
            })
            ->where('school_id', Auth::user()->school_id)
            ->get());
    }

    private function selectedBranchId(Request $request)
    {
        if (! moduleStatusCheck('Branch')) {
            return null;
        }

        return $request->branch_id ?: userBranch();
    }

    private function applyAssignSubjectBranchFilter($query, $branchId)
    {
        if (! moduleStatusCheck('Branch')) {
            return $query;
        }

        if (! $branchId) {
            return branchWise($query);
        }

        return $query->where(function ($query) use ($branchId): void {
            $query->where('branch_id', $branchId)
                ->orWhereNull('branch_id')
                ->orWhere('branch_id', 0);
        });
    }

    public function ajaxSelectSubject(Request $request)
    {

        $subject_all = branchWise(SmAssignSubject::where('class_id', '=', $request->class)->where('section_id', $request->section)->where('school_id', Auth::user()->school_id)->get())->unique('subject_id');
        $students = [];
        foreach ($subject_all as $allSubject) {
            $students[] = SmSubject::find($allSubject->subject_id);
        }

        return response()->json([$students]);
    }

    public function import()
    {

        $classes = branchWise(SmClass::get());

        return view('backEnd.academics.assign_subject_import', compact('classes'));

    }

    public function importStore(Request $request)
    {
        ini_set('max_execution_time', 0);
        $file = $request->file('file');
        $step = $request->get('step', 'upload');

        if ($step === 'upload' || $step === 'map') {
            $validate_rules = [
                'file' => 'required|mimes:csv,xls,xlsx|max:2048',
            ];
        } elseif ($step === 'import') {
            $validate_rules = [
                'index' => ['required', 'array'],
            ];
        } else {
            $validate_rules = [
            ];
        }

        $request->validate($validate_rules, validationMessage($validate_rules));

        $expectedHeaders = [
            'Subject',
            'Teacher',
        ];

        $requiredHeaders = [
            'subject',
            'teacher',
        ];

        $class = SmClass::findOrFail($request->class_id);
        $section = SmSection::findOrFail($request->section_id);
        if (shiftEnable()) {
            $shift = Shift::findOrFail($request->shift_id);
        } else {
            $shift = '';
        }

        if (moduleStatusCheck('Branch')) {
            $branch = Branch::findOrFail($request->branch_id);
        } else {
            $branch = '';
        }

        if ($step === 'upload') {
            $headers = (new HeadingRowImport())->toArray($file);

            $headers = $headers[0][0];
            $filteredHeaders = array_filter($headers, fn ($header): bool => ! is_numeric($header));
            $filteredHeaders = array_values($filteredHeaders);

            return view('backEnd.partials.subject-import._map', ['filteredHeaders' => $filteredHeaders, 'expectedHeaders' => $expectedHeaders, 'file' => $file, 'class' => $class, 'section' => $section, 'shift' => $shift, 'branch' => $branch]);
        }
        if ($step === 'map') {
            $allData = Excel::toArray(new BulkImport(), $file)[0];
            $mappedHeaders = json_decode((string) $request->get('headers'));
            $url = route('assign_subject_import_store');

            return view('backEnd.partials.subject-import._import', ['expectedHeaders' => $expectedHeaders, 'allData' => $allData, 'mappedHeaders' => $mappedHeaders, 'url' => $url, 'requiredHeaders' => $requiredHeaders, 'class' => $class, 'section' => $section, 'shift' => $shift, 'branch' => $branch]);
        }
        if ($step === 'import') {
            DB::beginTransaction();
            try {
                $count = count($request->subject);
                // Delete existing subject assignments ONCE before the loop
                SmAssignSubject::where('class_id', $request->class_id)
                    ->where('section_id', $request->section_id)
                    ->delete();
                for ($i = 0; $i < $count; $i++) {
                    // Try finding staff by email first
                    $staff = SmStaff::where('role_id', 4)
                        ->where('email', $request->teacher[$i])
                        ->first();
                    // If not found by email, try by staff number
                    if (! $staff) {
                        $staff = SmStaff::where('role_id', 4)
                            ->where('staff_no', $request->teacher[$i])
                            ->first();
                    }

                    if (! $staff) {
                        continue;
                    }
                    // Get the subject
                    $subject = SmSubject::where('subject_code', $request->subject[$i])->first();
                    if (! $subject) {
                        continue;
                    }
                    // Assign the subject
                    $assign_subject = new SmAssignSubject();
                    $assign_subject->class_id = $request->class_id;
                    $assign_subject->section_id = $request->section_id;
                    $assign_subject->shift_id = shiftEnable() ? $request->shift_id : null;
                    $assign_subject->subject_id = $subject->id;
                    $assign_subject->teacher_id = $staff->id;
                    $assign_subject->created_at = YearCheck::getYear().'-'.date('m-d h:i:s');
                    $assign_subject->academic_id = getAcademicId();
                    $assign_subject->school_id = Auth::user()->school_id;
                    if (moduleStatusCheck('Branch')) {
                        $assign_subject->branch_id = $request->branch_id;
                    }
                    $assign_subject->save();
                    event(new CreateClassGroupChat($assign_subject));
                }
                DB::commit();
                Toastr::success('Subjects imported successfully', 'Success');
            } catch (Exception $e) {
                DB::rollBack();
                Toastr::error('Import failed: '.$e->getMessage(), 'Error');
            }

            return redirect()->route('assign_subject_import');
        }

    }
}
