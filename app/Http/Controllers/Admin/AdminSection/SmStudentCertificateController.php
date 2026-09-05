<?php

namespace App\Http\Controllers\Admin\AdminSection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminSection\SmStudentCertificateRequest;
use App\Models\Graduate as ModelsGraduate;
use App\Models\SmClass;
use App\Models\SmStudentCertificate;
use App\Models\StudentRecord;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmStudentCertificateController extends Controller
{
    public function index()
    {
        /*
        try {
        */
        $select = ['id', 'name', 'header_left_text', 'date', 'body', 'body_two', 'certificate_no', 'type', 'footer_left_text', 'footer_center_text', 'footer_right_text', 'student_photo', 'file', 'layout', 'body_font_family', 'body_font_size', 'height', 'width', 'default_for', 'active_status'];
        if(moduleStatusCheck('Branch')){
            $select[] = 'branch_id';
        }
        $certificates = branchWise(SmStudentCertificate::where('active_status', 1)
            ->where('school_id', Auth::user()->school_id)
            ->select($select)
            ->get());

        return view('backEnd.admin.student_certificate', compact('certificates'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function store(SmStudentCertificateRequest $request)
    {
        // try {
        $destination = 'public/uploads/certificate/';
        $fileName = fileUpload($request->file, $destination);
        $certificate = new SmStudentCertificate();
        $certificate->name = $request->name;
        $certificate->header_left_text = $request->header_left_text;
        $certificate->date = date('Y-m-d', strtotime($request->date));
        $certificate->body = $request->body;
        $certificate->footer_left_text = $request->footer_left_text;
        $certificate->footer_center_text = $request->footer_center_text;
        $certificate->footer_right_text = $request->footer_right_text;
        $certificate->student_photo = $request->student_photo;
        $certificate->file = $fileName;
        $certificate->body_font_family = $request->body_font_family;
        $certificate->layout = $request->layout;
        $certificate->body_font_size = $request->body_font_size;
        $certificate->height = $request->height;
        $certificate->width = $request->width;
        $certificate->school_id = Auth::user()->school_id;
        if (moduleStatusCheck('Branch')) {
            $certificate->branch_id = $request->branch_id;
        }
        $certificate->academic_id = getAcademicId();

        $result = $certificate->save();

        Toastr::success('Operation successful', 'Success');

        return redirect()->back();

        // } catch (\Exception $e) {
        //     Toastr::error('Operation Failed', 'Failed');
        //     return redirect()->back();
        // }
    }

    public function edit($id)
    {

        /*
        try {
        */
        if (checkAdmin()) {
            $certificate = SmStudentCertificate::find($id);
        } else {
            $certificate = SmStudentCertificate::where('id', $id)->first();
        }
        $certificates = branchWise(SmStudentCertificate::where('school_id', Auth::user()->school_id)->get());

        return view('backEnd.admin.student_certificate', compact('certificates', 'certificate'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function update(SmStudentCertificateRequest $request, $id)
    {
        /*
        try {
        */
        $destination = 'public/uploads/certificate/';
        if (checkAdmin()) {
            $certificate = SmStudentCertificate::find($request->id);
        } else {
            $certificate = SmStudentCertificate::where('id', $request->id)->where('school_id', Auth::user()->school_id)->first();
        }
        $certificate->name = $request->name;
        $certificate->header_left_text = $request->header_left_text;
        $certificate->date = date('Y-m-d', strtotime($request->date));
        $certificate->body = $request->body;
        if ($request->body_two) {
            $certificate->body_two = $request->body_two;
        }
        $certificate->footer_left_text = $request->footer_left_text;
        $certificate->footer_center_text = $request->footer_center_text;
        $certificate->footer_right_text = $request->footer_right_text;
        $certificate->student_photo = $request->student_photo;
        $certificate->certificate_no = $request->certificate_no;
        $certificate->body_font_family = $request->body_font_family;
        $certificate->layout = $request->layout;
        $certificate->body_font_size = $request->body_font_size;
        $certificate->height = $request->height;
        $certificate->width = $request->width;
        if (moduleStatusCheck('Branch')) {
            $certificate->branch_id = $request->branch_id;
        }
        $certificate->file = fileUpdate($certificate->file, $request->file, $destination);
        // uest->all());
        $result = $certificate->save();

        Toastr::success('Operation successful', 'Success');

        return redirect('student-certificate');

        /*
        } catch (\Exception $e) {
             ;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function destroy($id)
    {

        /*
        try {
        */
        // $certificate = SmStudentCertificate::find($id);
        if (checkAdmin()) {
            $certificate = SmStudentCertificate::find($id);
        } else {
            $certificate = SmStudentCertificate::where('id', $id)->where('school_id', Auth::user()->school_id)->first();
        }
        if ($certificate->file) {
            unlink($certificate->file);
        }
        $result = $certificate->delete();

        Toastr::success('Operation successful', 'Success');

        return redirect('student-certificate');

        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    // for get route
    public function generateCertificate()
    {

        /*
        try {
        */
        $select = ['id', 'class_name'];
        if(moduleStatusCheck('Branch')){
            $select[] = 'branch_id';
        }
        $classes = branchWise(SmClass::where('active_status', 1)
            ->where('academic_id', getAcademicId())
            ->where('school_id', Auth::user()->school_id)
            ->select($select)
            ->get());
        $certificates = branchWise(SmStudentCertificate::where('school_id', auth()->user()->school_id)->get());

        return view('backEnd.admin.generate_certificate', compact('classes', 'certificates'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function setDefault($certificate_id, $default_for)
    {
        /*
        try {
        */
        $certificate = SmStudentCertificate::find($certificate_id);
        $certificate->default_for = $default_for;
        $result = $certificate->save();
        if ($result) {
            Toastr::success('Certificate Set As Default for '.$default_for, 'Success');

            return redirect()->back();
        }
        Toastr::error('Operation Failed', 'Failed');

        return redirect()->back();

        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function resetDefault($certificate_id)
    {
        /*
        try {
        */
        $certificate = SmStudentCertificate::find($certificate_id);
        $certificate->default_for = null;
        $result = $certificate->save();
        if ($result) {
            Toastr::success('Certificate Reset For Default', 'Success');

            return redirect()->back();
        }
        Toastr::error('Operation Failed', 'Failed');

        return redirect()->back();

        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    // for post route
    public function generateCertificateSearch(Request $request)
    {
        /*
         try {
         */
        $certificate_id = $request->certificate;
        $branch_id = $request->branch_id ?? null;
        $select = ['id', 'name', 'header_left_text', 'date', 'body', 'body_two', 'certificate_no', 'type', 'footer_left_text', 'footer_center_text', 'footer_right_text', 'student_photo', 'file', 'layout', 'body_font_family', 'body_font_size', 'height', 'width', 'default_for', 'active_status'];
        if(moduleStatusCheck('Branch')){
            $select[] = 'branch_id';
        }
        $certificates = branchWise(SmStudentCertificate::where('active_status', 1)->where('school_id', Auth::user()->school_id)->select($select)->get());
        if (moduleStatusCheck('University')) {
            $graduates = ModelsGraduate::query();
            $graduates->when($request->un_session_id, function ($query) use ($request) {
                $query->where('un_session_id', $request->un_session_id);
            });
            $graduates->when($request->un_faculty_id, function ($query) use ($request) {
                $query->where('un_faculty_id', $request->un_faculty_id);
            });
            $graduates->when($request->un_department_id, function ($query) use ($request) {
                $query->where('un_department_id', $request->un_department_id);
            });
            $graduates->when($request->branch_id, function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);
            });
            $graduates = $graduates->where('school_id', auth()->user()->school_id)
                ->with('student', 'unSession', 'unFaculty', 'unDepartment')->get();

            $un_session_id = $request->un_session_id;
            $un_faculty_id = $request->un_faculty_id;
            $un_department_id = $request->un_department_id;

            return view('university::un_generate_certificate', compact('graduates', 'certificates', 'certificate_id', 'un_session_id', 'un_faculty_id', 'un_department_id', 'branch_id'));

        }
        $shift_id = $request->shift;
        $class_id = $request->class;
        $section_id = $request->section;

        $classes = SmClass::where('active_status', 1)->where('academic_id', getAcademicId())->where('school_id', Auth::user()->school_id)->get();

        $students = StudentRecord::when($request->academic_year, function ($query) use ($request) {
            $query->where('academic_id', $request->academic_year);
        })
            ->when($request->class, function ($query) use ($request) {
                $query->where('class_id', $request->class);
            })
            ->when($request->section, function ($query) use ($request) {
                $query->where('section_id', $request->section);
            })
            ->when($request->shift, function ($query) use ($request) {
                $query->where('shift_id', $request->shift);
            })
            ->when(! $request->academic_year, function ($query) {
                $query->where('academic_id', getAcademicId());
            })
            ->when($request->branch, function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id);
            })->where('school_id', auth()->user()->school_id)->get();

        return view('backEnd.admin.generate_certificate', compact('classes', 'certificates', 'certificate_id', 'certificates', 'students', 'class_id', 'section_id', 'shift_id', 'branch_id'));

        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function generateCertificateGenerate($s_id, $c_id)
    {
        /*
        try {
        */
        $s_ids = explode('-', $s_id);
        $students = [];
        foreach ($s_ids as $sId) {
            $std = StudentRecord::with('student')->find($sId);
            if ($std) {
                $students[] = $std;
            }

        }

        $certificate = SmStudentCertificate::find($c_id);

        if (moduleStatusCheck('University')) {
            if ($certificate && $certificate->type === 'arabic') {
                return view('backEnd.admin.generate_arabic_certificate', compact('students', 'certificate'));
            }
        }

        return view('backEnd.admin.student_certificate_print', ['students' => $students, 'certificate' => $certificate]);

        /*
        } catch (\Exception $e) {

            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }
}
