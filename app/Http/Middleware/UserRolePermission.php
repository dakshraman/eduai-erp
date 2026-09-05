<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Modules\RolePermission\Entities\Permission;

class UserRolePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next, $route = null)
    {

        
        if (! Auth::guard('web')->check()) {
            return redirect()->route('login');
        }

        $permissions = app('permission');

        $user = auth()->user();
        if ($user->role_id === 3 && Cache::get('have_due_fees_'.$user->id)) {
            $url = explode('/', $request->getRequestUri());
            $param = end($url);
            $students = Cache::get('have_due_fees_'.$user->id);

            if (in_array($param, $students) && ! in_array(Route::currentRouteName(), ['parent_fees', 'fees.student-fees-list-parent'])) {

                abort(403);
            } else {
                return $next($request);
            }
        }

        if (! $this->hasPermission($route)) {
            abort(403);
        }

        if ((! is_null($permissions)) && (Auth::user()->role_id !== 1)) {

            if (in_array($route, $permissions)) {
                return $next($request);
            }

            abort('403');

        } else {
            return $next($request);
        }

        return null;
    }

    public function hasPermission($route)
    {
        $parent_module_id = $this->permissionIdForRoute($route);

        if ($parent_module_id) {
            // get permission name
            $school_permissions = planPermissions('menus', true);
            $key = false;
            foreach ($school_permissions as $permission => $id) {
                if ($id === $parent_module_id) {
                    $key = $permission;
                    break;
                }
            }

            if ($key) {
                return isMenuAllowToShow($key);
            }
        }

        return true;
    }

    private function permissionIdForRoute($route): ?int
    {
        if (! $route || ! auth()->check()) {
            return null;
        }

        $permissions = $this->permissionRouteMap();

        return $permissions[$route] ?? null;
    }

    private function permissionRouteMap(): array
    {
        $user = auth()->user();
        $school_id = $user->school_id ?? 1;
        $version = function_exists('permissionMenuCacheVersion') ? permissionMenuCacheVersion($school_id) : 1;
        $role_type = session()->get('role_permission_user_type', $user->role_id);
        $key = 'permission_route_map_user_'.$user->id.'_role_'.$user->role_id.'_type_'.$role_type.'_school_'.$school_id.'_v_'.$version;

        return Cache::remember($key, 600, function () {
            return Permission::query()
                ->whereNotNull('route')
                ->where('route', '!=', '')
                ->pluck('id', 'route')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        });
    }
}
