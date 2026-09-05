<?php

namespace App\Http\Controllers\Admin\AdminSection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminSection\SmStudentIdCardRequest;
use App\Models\Role;
use App\Models\SmClass;
use App\Models\SmParent;
use App\Models\SmStaff;
use App\Models\SmStudent;
use App\Models\SmStudentIdCard;
use Barryvdh\DomPDF\Facade\Pdf;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\RolePermission\Entities\InfixRole;

class SmStudentIdCardController extends Controller
{
    public function index()
    {
        /*
        try {
        */
        if (moduleStatusCheck('Branch')) {
            $id_cards = branchWise(SmStudentIdCard::where('active_status', 1)->where('school_id', Auth::user()->school_id)->select(['id', 'title', 'role_id', 'branch_id'])->get());
        } else {
            $id_cards = SmStudentIdCard::where('active_status', 1)->where('school_id', Auth::user()->school_id)->select(['id', 'title', 'role_id'])->get();
        }

        return view('backEnd.admin.idCard.student_id_card_list', compact('id_cards'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function create_id_card()
    {
        /*
        try{
        */
        $user = Auth::user();
        $id_cards = branchWise(SmStudentIdCard::where('active_status', 1)->where('school_id', $user->school_id)->get());
        $roles = InfixRole::select('*')->where('is_saas', 0)->where('id', '!=', 1)->where(function ($q) use ($user) {
            $q->where('school_id', $user->school_id)->orWhere('type', 'System');
        })->get();

        return view('backEnd.admin.idCard.student_id_card', compact('id_cards', 'roles'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function store(SmStudentIdCardRequest $request)
    {
        /*
        try {
        */

        $destination = 'public/uploads/studentIdCard/';
        $id_card = new SmStudentIdCard();
        $id_card->title = $request->title;
        $id_card->logo = $request->logo ? fileUpload($request->logo, $destination) : 'public/backEnd/id_card/img/logo.png';
        $id_card->school_id = Auth::user()->school_id;
        $id_card->academic_id = getAcademicId();
        if (moduleStatusCheck('University')) {
            $id_card->un_academic_id = getAcademicId();
        }
        if (moduleStatusCheck('Branch')) {
            $id_card->branch_id = $request->branch_id;
        }
        $id_card->signature = fileUpload($request->signature, $destination);
        $id_card->background_img = fileUpload($request->background_img, $destination);
        $id_card->profile_image = fileUpload($request->profile_image, $destination);
        if (in_array(2, $request->applicable_user) || in_array(3, $request->applicable_user)) {
            $id_card->role_id = json_encode($request->applicable_user);
        } else {
            $id_card->role_id = json_encode($request->role);
        }

        $id_card->page_layout_style = $request->page_layout_style;
        $id_card->user_photo_style = $request->user_photo_style;
        $id_card->user_photo_width = $request->user_photo_width;
        $id_card->user_photo_height = $request->user_photo_height;
        $id_card->pl_width = $request->pl_width;
        $id_card->pl_height = $request->pl_height;
        $id_card->t_space = $request->t_space;
        $id_card->b_space = $request->b_space;
        $id_card->l_space = $request->l_space;
        $id_card->r_space = $request->r_space;
        $id_card->admission_no = $request->admission_no;
        $id_card->student_name = $request->student_name;
        $id_card->class = $request->class ?? 0;
        if (moduleStatusCheck('University')) {
            $id_card->un_session = $request->un_session_id;
            $id_card->un_faculty = $request->un_faculty_id;
            $id_card->un_department = $request->un_department_id;
            $id_card->un_academic = $request->un_academic_id;
            $id_card->un_semester = $request->un_semester_id;
            $id_card->un_semester_label = $request->un_semester_label_id;
        }
        $id_card->father_name = $request->father_name;
        $id_card->mother_name = $request->mother_name;
        $id_card->student_address = $request->student_address;
        $id_card->dob = $request->dob;
        $id_card->blood = $request->blood;
        $id_card->photo = $request->photo;
        $id_card->signature_status = $request->signature_status;
        $id_card->staff_department = $request->staff_department;
        $id_card->staff_designation = $request->staff_designation;
        if (in_array(3, $request->applicable_user)) {
            $id_card->phone_number = $request->phone_number;
        }

        $id_card->save();

        Toastr::success('Operation successful', 'Success');

        return redirect('student-id-card');

        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function edit($id)
    {
        /*
        try {
        */
        $id_cards = branchWise(SmStudentIdCard::get());
        $roles = InfixRole::select('*')->where('is_saas', 0)->where('id', '!=', 1)->where(function ($q) {
            $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
        })->get();
        $id_card = SmStudentIdCard::find($id);

        return view('backEnd.admin.idCard.student_id_card', compact('id_cards', 'id_card', 'roles'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function previewIdCard($id)
    {
        /*
        try{
        */
        $id_card = SmStudentIdCard::where('id', $id)->first();
        if ($id_card) {
            $roles = InfixRole::select('*')->where('is_saas', 0)->where('id', '!=', 1)->where(function ($q) {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })->get();
            $view = view('backEnd.admin.idCard.id_cart_preview_modal', compact('id_card', 'roles'))->render();

            return response()->json([
                'view' => $view,
                'status' => 1,
            ]);
        }

        return response()->json([
            'status' => 0,
            'msg' => 'Not Found',
        ]);

        /*
        }catch(\Exception $e){
            return response()->json([
                "status" => 0,
                "msg" => 'Operation Failed'
            ]);
        }
        */
    }

    public function update(SmStudentIdCardRequest $request, $id)
    {
        /*
        try {
        */
        $destination = 'public/uploads/studentIdCard/';
        $id_card = SmStudentIdCard::find($request->id);
        $id_card->title = $request->title;
        $id_card->academic_id = getAcademicId();
        $id_card->logo = $request->old_logo === 0 ? 'public/backEnd/id_card/img/logo.png' : fileUpdate($id_card->logo, $request->logo, $destination);
        $id_card->background_img = $request->old_bg === 0 ? 'public/backEnd/id_card/img/vertical_bg.png' : fileUpdate($id_card->background_img, $request->background_img, $destination);
        $id_card->profile_image = $request->old_profile === 0 ? 'public/backEnd/id_card/img/thumb.png' : fileUpdate($id_card->profile_image, $request->profile_image, $destination);
        if (in_array(2, $request->applicable_user) || in_array(3, $request->applicable_user)) {
            $id_card->role_id = json_encode($request->applicable_user);
        } else {
            $id_card->role_id = json_encode($request->role);
        }
        $id_card->signature = $request->old_sign === 0 ? 'public/backEnd/id_card/img/Signature.png' : fileUpdate($id_card->signature, $request->signature, $destination);
        $id_card->page_layout_style = $request->page_layout_style;
        $id_card->user_photo_style = $request->user_photo_style;
        $id_card->user_photo_width = $request->user_photo_width;
        $id_card->user_photo_height = $request->user_photo_height;
        $id_card->pl_width = $request->pl_width;
        $id_card->pl_height = $request->pl_height;
        $id_card->t_space = $request->t_space;
        $id_card->b_space = $request->b_space;
        $id_card->l_space = $request->l_space;
        $id_card->r_space = $request->r_space;
        $id_card->admission_no = $request->admission_no;
        $id_card->student_name = $request->student_name;
        $id_card->class = $request->class;
        $id_card->father_name = $request->father_name;
        $id_card->mother_name = $request->mother_name;
        $id_card->student_address = $request->student_address;
        $id_card->dob = $request->dob;
        $id_card->blood = $request->blood;
        $id_card->photo = $request->photo;
        $id_card->signature_status = $request->signature_status;
        $id_card->staff_department = $request->staff_department;
        $id_card->staff_designation = $request->staff_designation;
        if (moduleStatusCheck('Branch')) {
            $id_card->branch_id = $request->branch_id;
        }
        if (moduleStatusCheck('University')) {
            $id_card->un_session = $request->un_session_id;
            $id_card->un_faculty = $request->un_faculty_id;
            $id_card->un_department = $request->un_department_id;
            $id_card->un_academic = $request->un_academic_id;
            $id_card->un_semester = $request->un_semester_id;
            $id_card->un_semester_label = $request->un_semester_label_id;
        }

        if (in_array(3, $request->applicable_user)) {
            $id_card->phone_number = $request->phone_number;
        }
        $id_card->save();
        Toastr::success('Operation successful', 'Success');

        return redirect('student-id-card');
        /*
        } catch (\Exception $e) {
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
        $id_card = SmStudentIdCard::find($request->id);

        if ($id_card->logo  && file_exists($id_card->logo)) {
            unlink($id_card->logo);
        }

        if ($id_card->signature  && file_exists($id_card->signature)) {
            unlink($id_card->signature);
        }

        if ($id_card->profile_image  && file_exists($id_card->profile_image)) {
            unlink($id_card->profile_image);
        }

        if ($id_card->background_img  && file_exists($id_card->background_img)) {
            unlink($id_card->background_img);
        }

        $id_card->delete();
        Toastr::success('Operation successful', 'Success');

        return redirect('student-id-card');
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function generateIdCard()
    {
        /*
        try {
        */
        $id_cards = branchWise(SmStudentIdCard::select(['id', 'title'])->get());
        $roles = Role::get();
        $classes = branchWise(SmClass::get());

        return view('backEnd.admin.idCard.generate_id_card', compact('id_cards', 'roles', 'classes'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function generateIdCardBulk(Request $request)
    {
        $request->validate([
            'role' => 'required',
            'id_card' => 'required',
            'grid_gap' => 'required',
        ]);

        if ($request->role == 2) {
            if (moduleStatusCheck('University')) {
                $studentQuery = SmStudent::when($request->un_session_id, function ($q) use ($request) {
                    $q->whereHas('studentRecord', function ($query) use ($request) {
                        $query->where('un_session_id', $request->un_session_id);
                    });
                })
                    ->when($request->un_faculty_id, function ($q) use ($request) {
                        $q->whereHas('studentRecord', function ($query) use ($request) {
                            $query->where('un_faculty_id', $request->un_faculty_id);
                        });
                    })
                    ->when($request->un_department_id, function ($q) use ($request) {
                        $q->whereHas('studentRecord', function ($query) use ($request) {
                            $query->where('un_department_id', $request->un_department_id);
                        });
                    })
                    ->when($request->un_semester_id, function ($q) use ($request) {
                        $q->whereHas('studentRecord', function ($query) use ($request) {
                            $query->where('un_semester_id', $request->un_semester_id);
                        });
                    })
                    ->when($request->un_semester_label_id, function ($q) use ($request) {
                        $q->whereHas('studentRecord', function ($query) use ($request) {
                            $query->where('un_semester_label_id', $request->un_semester_label_id);
                        });
                    })->when($request->branch_id, function ($q) use ($request) {
                        $q->whereHas('studentRecord', function ($query) use ($request) {
                            $query->where('branch_id', $request->branch_id);
                        });
                    })
                    ->get();
                $s_students = moduleStatusCheck('Branch') && $request->branch_id ? $studentQuery : branchWise($studentQuery);

            } else {
                $studentQuery = SmStudent::when($request->class, function ($q) use ($request) {
                    $q->whereHas('studentRecord', function ($query) use ($request) {
                        $query->where('class_id', $request->class);
                    });
                })->when($request->section, function ($q) use ($request) {
                    $q->whereHas('studentRecord', function ($query) use ($request) {
                        $query->where('section_id', $request->section);
                    });
                })->when($request->branch_id, function ($q) use ($request) {
                    $q->whereHas('studentRecord', function ($query) use ($request) {
                        $query->where('branch_id', $request->branch_id);
                    });
                })
                ->when($request->shift, function ($q) use ($request) {
                    $q->whereHas('studentRecord', function ($query) use ($request) {
                        $query->where('shift_id', $request->shift);
                    });
                })
                    ->with('parents', 'bloodGroup')
                    ->get();
                $s_students = moduleStatusCheck('Branch') && $request->branch_id ? $studentQuery : branchWise($studentQuery);

            }

        } elseif ($request->role == 3) {

            $studentGuardianQuery = SmStudent::when(moduleStatusCheck('Branch') && $request->branch_id, function ($q) use ($request) {
                $q->whereHas('studentRecord', function ($query) use ($request) {
                    $query->where('branch_id', $request->branch_id);
                });
            })->pluck('parent_id');
            $studentGuardian = moduleStatusCheck('Branch') && $request->branch_id ? $studentGuardianQuery : branchWise($studentGuardianQuery);
            $guardianQuery = SmParent::whereIn('id', $studentGuardian)->get();
            $s_students = moduleStatusCheck('Branch') && $request->branch_id ? $guardianQuery : branchWise($guardianQuery);
        } else {
            $staffQuery = SmStaff::whereRole($request->role)
                ->when(moduleStatusCheck('Branch') && $request->branch_id, function ($query) use ($request) {
                    $query->where('branch_id', $request->branch_id);
                })
                ->status()
                ->get();
            $s_students = moduleStatusCheck('Branch') && $request->branch_id ? $staffQuery : branchWise($staffQuery);
        }
        $id_card = SmStudentIdCard::status()->find($request->id_card);
        if (! $id_card) {
            Toastr::error('ID card not found for selected academic year or branch', 'Failed');

            return redirect()->back();
        }

        $role_id = $request->role;
        $gridGap = $request->grid_gap;
        $branch_id = $request->branch_id ?? null;
        $class_id = $request->class ?? null;
        $section_id = $request->section ?? null;
        $shift_id = $request->shift ?? null;

        return view('backEnd.admin.idCard.student_id_card_print_bulk', ['id_card' => $id_card, 's_students' => $s_students, 'role_id' => $role_id, 'gridGap' => $gridGap, 'branch_id' => $branch_id, 'class_id' => $class_id, 'section_id' => $section_id, 'shift_id' => $shift_id]);

        $pdf = Pdf::loadView('backEnd.admin.student_id_card_print_2', ['id_card' => $id_card, 's_students' => $s_students]);
        return $pdf->stream($id_card->title . '.pdf');
    }

    public function ajaxIdCard(Request $request)
    {
        /*
        try {
        */

        $role_id = $request->role_id;
        $idCardQuery = SmStudentIdCard::status();
        if (moduleStatusCheck('Branch') && $request->branch_id) {
            $idCardQuery->where(function ($query) use ($request) {
                $query->where('branch_id', $request->branch_id)
                    ->orWhereNull('branch_id')
                    ->orWhere('branch_id', 0);
            });
        }

        $id_cards = moduleStatusCheck('Branch') && $request->branch_id
            ? $idCardQuery->get()
            : branchWise($idCardQuery->get());
        $idCards = [];
        foreach ($id_cards as $id_card) {
            $role_ids = json_decode($id_card->role_id, true) ?: [];
            if (in_array($role_id, $role_ids)) {
                $d['id'] = $id_card->id;
                $d['title'] = $id_card->title;
                $idCards[] = $d;
            }
        }

        return response()->json([$idCards]);

        /*
        } catch (\Throwable $th) {

        }
        */
    }

    public function generateIdCardSearch(Request $request)
    {
        return $request->all();

        $request->validate([
            'class' => 'required',
            'id_card' => 'required',
        ]);

        /*
        try {
        */
        $card_id = $request->id_card;
        $class_id = $request->class;
        $students = branchWise(SmStudent::with('class', 'parents', 'section', 'gender')->get());
        $classes = branchWise(SmClass::get());
        $id_cards = branchWise(SmStudentIdCard::get());

        return view('backEnd.admin.idCard.generate_id_card_old', compact('id_cards', 'class_id', 'classes', 'students', 'card_id', 'section'));
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function ajaxStudentIdCardPrint()
    {
        /*
        try {
        */
        $pdf = Pdf::loadView('backEnd.admin.idCard.student_id_card_print');

        return response()->$pdf->stream('certificate.pdf');
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }

    public function generateIdCardPrint($s_id, $c_id)
    {
        set_time_limit(2700);
        /*
        try {
        */
        $s_ids = explode('-', $s_id);
        $students = [];
        foreach ($s_ids as $sId) {
            $students[] = SmStudent::find($sId);
        }
        $id_card = SmStudentIdCard::find($c_id);

        return view('backEnd.admin.idCard.student_id_card_print_2', ['id_card' => $id_card, 'students' => $students]);
        $pdf = Pdf::loadView('backEnd.admin.idCard.student_id_card_print_2', ['id_card' => $id_card, 'students' => $students]);

        return $pdf->stream($id_card->title.'.pdf');
        /*
        } catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
        */
    }
}
