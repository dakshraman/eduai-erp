<?php

namespace App\Http\Controllers;

use App\Imports\BulkImport;
use App\Models\SmBaseSetup;
use App\Models\SmDesignation;
use App\Models\SmHumanDepartment;
use App\Models\SmStaff;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Modules\RolePermission\Entities\InfixRole;

class ImportController extends Controller
{
    public function index()
    {
        $data['genders'] = SmBaseSetup::where('base_group_id', '=', '1')->get(['id', 'base_setup_name']);
        $data['roles'] = InfixRole::where('is_saas', 0)
            ->where('active_status', 1)
            ->where(function ($q): void {
                $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
            })
            ->whereNotIn('id', [1, 2, 3])
            ->orderBy('name', 'asc')
            ->get();

        $data['departments'] = SmHumanDepartment::where('is_saas', 0)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        $data['designations'] = SmDesignation::where('is_saas', 0)
            ->orderBy('title', 'asc')
            ->get(['id', 'title']);

        return view('backEnd.humanResource.import_staff', $data);
    }

    public function staffStore(Request $request)
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
            'Staff No',
            'Role',
            'Department',
            'Designation',
            'First Name',
            'Last Name',
            'Fathers Name',
            'Mothers Name',
            'Date of Birth',
            'Date of Joining',
            'Email',
            'Gender Id',
            'Mobile',
            'Emergency Mobile',
            'Marital Status',
            'Current Address',
            'Permanent Address',
            'Qualification',
            'Experience',
            'Epf No',
            'Basic Salary',
            'Contract Type',
            'Location',
            'Bank Account Name',
            'Bank Account No',
            'Bank Name',
            'Bank Branch',
            'Facebook Url',
            'Twitter Url',
            'Instagram Url',
            'Driving License',
        ];

        $requiredHeaders = [
            'staff_no',
            'role',
            'first_name',
            'email',
        ];
        if ($step === 'upload') {
            $headers = (new HeadingRowImport())->toArray($file);

            $headers = $headers[0][0];
            $filteredHeaders = array_filter($headers, fn ($header): bool => ! is_numeric($header));
            $filteredHeaders = array_values($filteredHeaders);

            return view('backEnd.partials.import._map', ['filteredHeaders' => $filteredHeaders, 'expectedHeaders' => $expectedHeaders, 'file' => $file]);
        }
        if ($step === 'map') {
            $allData = Excel::toArray(new BulkImport(), $file)[0];
            $mappedHeaders = json_decode((string) $request->get('headers'));
            $url = route('staff-bulk-store');

            return view('backEnd.partials.import._import', ['expectedHeaders' => $expectedHeaders, 'allData' => $allData, 'mappedHeaders' => $mappedHeaders, 'url' => $url, 'requiredHeaders' => $requiredHeaders]);
        }

        // Implement Main Code
        if ($step === 'import') {
            DB::beginTransaction();
            try {
                $count = count($request->staff_no);

                for ($i = 0; $i < $count; $i++) {
                    $email = $request->email[$i];

                    // Duplicate email check
                    if (User::where('email', $email)->exists()) {
                        Toastr::error('Duplicate email found: '.$email);

                        continue;
                    }

                    // Role ID
                    $role_id = InfixRole::where('is_saas', 0)
                        ->where('name', $request->role[$i])
                        ->where(function ($q) {
                            $q->where('school_id', auth()->user()->school_id)
                                ->orWhere('type', 'System');
                        })
                        ->value('id');

                    if (! $role_id) {
                        Toastr::error('Invalid role: '.$request->role[$i]);

                        continue;
                    }

                    // Subscription limit
                    if (isSubscriptionEnabled()) {
                        $active_staff = SmStaff::where('school_id', auth()->user()->school_id)
                            ->where('active_status', 1)
                            ->count();

                        if (\Modules\Saas\Entities\SmPackagePlan::staff_limit() <= $active_staff && saasDomain() !== 'school') {
                            DB::commit();
                            Toastr::error('Your staff limit has been exceeded.', 'Failed');

                            return redirect()->route('staff_directory');
                        }
                    }

                    // Create User
                    $user = new User();
                    $user->role_id = $role_id === 1 ? 5 : $role_id;
                    $user->username = $request->mobile[$i] ?: $email;
                    $user->email = $email;
                    $user->phone_number = $request->mobile[$i];
                    $user->full_name = $request->first_name[$i].' '.$request->last_name[$i];
                    $user->password = Hash::make(123456);
                    $user->school_id = auth()->user()->school_id;
                    $user->save();

                    if ($role_id === 5) {
                        $this->assignChatGroup($user);
                    }

                    // Department and Designation
                    $department_id = SmHumanDepartment::where('name', $request->department[$i])->value('id');
                    $designation_id = SmDesignation::where('title', $request->designation[$i])->value('id');

                    // Create Staff
                    $staff = new SmStaff();
                    $staff->staff_no = $request->staff_no[$i];
                    $staff->role_id = $role_id === 1 ? 5 : $role_id;
                    $staff->department_id = $department_id;
                    $staff->designation_id = $designation_id;
                    $staff->first_name = $request->first_name[$i];
                    $staff->last_name = $request->last_name[$i];
                    $staff->full_name = $request->first_name[$i].' '.$request->last_name[$i];
                    $staff->fathers_name = $request->fathers_name[$i];
                    $staff->mothers_name = $request->mothers_name[$i];
                    $staff->email = $email;
                    $staff->school_id = auth()->user()->school_id;
                    $staff->gender_id = $request->gender_id[$i];
                    $staff->marital_status = $request->marital_status[$i];

                    $staff->date_of_birth = $this->convertExcelDate($request->date_of_birth[$i]);
                    $staff->date_of_joining = $this->convertExcelDate($request->date_of_joining[$i]);

                    $staff->mobile = $request->mobile[$i];
                    $staff->emergency_mobile = $request->emergency_mobile[$i];
                    $staff->current_address = $request->current_address[$i];
                    $staff->permanent_address = $request->permanent_address[$i];
                    $staff->qualification = $request->qualification[$i];
                    $staff->experience = $request->experience[$i];
                    $staff->epf_no = $request->epf_no[$i] ?? null;
                    $staff->basic_salary = $request->basic_salary[$i] ?? 0;
                    $staff->contract_type = $request->contract_type[$i];
                    $staff->user_id = $user->id;
                    $staff->location = $request->location[$i] ?? null;
                    $staff->bank_account_name = $request->bank_account_name[$i] ?? null;
                    $staff->bank_account_no = $request->bank_account_no[$i] ?? null;
                    $staff->bank_name = $request->bank_name[$i] ?? null;
                    $staff->bank_brach = $request->bank_branch[$i] ?? null;
                    $staff->facebook_url = $request->facebook_url[$i] ?? null;
                    $staff->twiteer_url = $request->twitter_url[$i] ?? null;
                    $staff->linkedin_url = $request->linkedin_url[$i] ?? null;
                    $staff->instragram_url = $request->instagram_url[$i] ?? null;
                    $staff->driving_license = $request->driving_license[$i] ?? null;

                    $staff->save();
                }

                DB::commit();
                Toastr::success('Import file added successfully', 'success');
            } catch (Exception $e) {
                DB::rollBack();
                Toastr::success('Import file added failed', 'error');
            }

            return redirect()->route('import-staff');
        }
    }

    private function assignChatGroup(User $user): void
    {
        $groups = \Modules\Chat\Entities\Group::where('school_id', auth()->user()->school_id)->get();
        foreach ($groups as $group) {
            createGroupUser($group, $user->id);
        }
    }

    private function convertExcelDate($excelDate)
    {
        if (is_numeric($excelDate)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($excelDate)->format('Y-m-d');
        }

        return date('Y-m-d', strtotime($excelDate));
    }
}
