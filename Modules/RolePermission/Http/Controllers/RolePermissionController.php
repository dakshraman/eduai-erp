<?php

namespace Modules\RolePermission\Http\Controllers;

use App\Models\ApiBaseMethod;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\RolePermission\Entities\AssignPermission;
use Modules\RolePermission\Entities\InfixModuleInfo;
use Modules\RolePermission\Entities\InfixRole;
use Modules\RolePermission\Entities\Permission;
use Modules\RolePermission\Http\Requests\RoleRequest;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        return view('rolepermission::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('rolepermission::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {

        return view('rolepermission::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        return view('rolepermission::edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function role(Request $request)
    {
        try {

            $roles = InfixRole::where('is_saas', 0)->when((generalSetting()->with_guardian !== 1), function ($query) {
                $query->where('id', '!=', 3);
            })->where('active_status', '=', 1)
                ->where(function ($q) {
                    $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
                })
                ->where('id', '!=', 1)
                ->orderBy('id', 'desc')
                ->get();
            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                return ApiBaseMethod::sendResponse($roles, null);
            }

            return view('rolepermission::role', compact('roles'));
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function roleStore(RoleRequest $request)
    {

        try {

            // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $role = new InfixRole();
            $role->name = $request->name;
            $role->type = 'User Defined';
            $role->school_id = Auth::user()->school_id;
            $role->save();

            Toastr::success('Operation successful', 'Success');

            return redirect()->back();

        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function roleEdit(Request $request, $id)
    {
        try {
            // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $role = InfixRole::find($id);
            $roles = InfixRole::where('is_saas', 0)->where('active_status', '=', 1)
                ->where(function ($q) {
                    $q->where('school_id', Auth::user()->school_id)->orWhere('type', 'System');
                })
                ->where('id', '!=', 1)
                ->orderBy('id', 'desc')
                ->get();

            if (ApiBaseMethod::checkUrl($request->fullUrl())) {
                $data = [];
                $data['role'] = $role;
                $data['roles'] = $roles->toArray();

                return ApiBaseMethod::sendResponse($data, null);
            }

            return view('rolepermission::role', compact('role', 'roles'));
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function roleUpdate(RoleRequest $request)
    {

        try {
            // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $role = InfixRole::find($request->id);
            $role->name = $request->name;
            $result = $role->save();

            Toastr::success('Operation successful', 'Success');

            return redirect()->route('rolepermission/role');

        } catch (Exception $e) {

            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function roleDelete(Request $request)
    {
        try {
            $tables = \App\Support\tableList::getTableList('role_id', $request->id);
            if ($tables === null || $tables === '') {
                $role = InfixRole::find($request->id);
                if ($role && $role->type !== 'System') {
                    $role->delete();
                    Toastr::success('Successfully Deleted', 'Success');

                    return redirect()->back();
                }
                Toastr::error('System Default roles can\'t be deleted', 'Failed');

                return redirect()->back();

            }
            $msg = 'This data already used in  : '.$tables.' Please remove those data first';
            Toastr::error($msg, 'Failed');

            return redirect()->back();

        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function assignPermission(int $id)
    {

        try {

            // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $role = InfixRole::with('assignedPermission')->where('is_saas', 0)->where('id', $id)->first();
            $already_assigned = $role->assignedPermission->pluck('permission_id')->toArray();

            if ($id === 2 || $id === 3) {
                session()->put('role_permission_user_type', $id);
                $all_permissions = Permission::with('subModule.subModule')
                    ->whereNull('custom_menu_id')
                    ->whereNull('parent_route')->whereNotNull('route')->where('route', '!=', '')
                    ->where('is_saas', 0)
                    ->whereNotInDeaActiveModulePermission()
                    ->where('menu_status', 1)
                    ->where('permission_section', '!=', 1)
                    ->when($id === 2, function ($q) {
                        $q->where('is_student', 1);
                    })->when($id === 3, function ($q) {
                        $q->where('is_parent', 1);
                    })
                    ->where('role_id', null)
                    ->orderBy('position', 'ASC')
                    ->get();

                return view('rolepermission::role_permission_student', compact('role', 'all_permissions', 'already_assigned'));
            }
            if ($id !== 2 && $id !== 3) {
                session()->put('role_permission_user_type', '');
                $all_permissions = Permission::with('subModule.subModule')
                    ->whereNull('custom_menu_id')
                    ->where('is_saas', 0)
                    ->whereNull('parent_route')
                    ->where(function ($q) {
                        $q->where('permission_section', '!=', 1)->orWhereNull('permission_section');
                    })
                    ->whereNotNull('route')->where('route', '!=', '')
                    ->whereNotInDeaActiveModulePermission()
                    ->where('menu_status', 1)
                    ->when(generalSetting()->fees_status === 1, function ($q) {
                        $q->where(function ($subQ) {
                            $subQ->where('module', '!=', 'fees_collection')->orWhereNull('module');
                        });
                    })
                    ->when(generalSetting()->fees_status === 0, function ($q) {
                        $q->where(function ($q) {
                            $q->where('module', '!=', 'Fees')->orWhereNull('module');
                        });
                    })
                    ->where('is_admin', 1)
                    ->where('role_id', null)
                    ->orderBy('position', 'ASC')
                    ->get();
                //                dd($all_permissions);

                return view('rolepermission::role_permission', compact('role', 'all_permissions', 'already_assigned'));
            }
        } catch (Exception $e) {
            Toastr::error($e->getMessage(), 'Failed');
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }

    public function rolePermissionAssign(Request $request)
    {

        DB::beginTransaction();

        try {
            Schema::disableForeignKeyConstraints();
            // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            AssignPermission::where('school_id', Auth::user()->school_id)->where('role_id', $request->role_id)->delete();

            $permissions = [];
            if ($request->has('permissions_string') && !empty($request->permissions_string)) {
                $permissions = array_filter(explode(',', $request->permissions_string));
            } elseif (is_array($request->module_id)) {
                $permissions = $request->module_id;
            }

            if (!empty($permissions)) {
                foreach ($permissions as $permission) {

                    $assign = new AssignPermission();
                    $assign->permission_id = $permission;
                    $assign->role_id = $request->role_id;
                    $assign->school_id = Auth::user()->school_id;
                    $assign->save();
                }
            }

            DB::commit();
            if (function_exists('clearPermissionMenuCache')) {
                clearPermissionMenuCache(Auth::user()->school_id);
            }
            // Toastr::success('User must be relogin again for applied permission changes', 'Success');
            Toastr::success('User permission applied successfully', 'Success');

            return redirect('rolepermission/role');
        } catch (Exception $e) {
            DB::rollback();
            Toastr::error($e->getMessage(), 'Failed');

            return redirect()->back();
        }
    }

    private function getPermissionList()
    {
        $activeModuleList = InfixModuleInfo::whereNull('parent_route')->where('active_status', 1)->get();
    }
}
