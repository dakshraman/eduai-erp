<?php

namespace App\Http\Controllers;

use App\Models\SmStudent;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\MenuManage\Entities\SmMenu;

class SmSearchController extends Controller
{
    public function search(Request $request)
    {
        try {
            if ($request->ajax()) {
                $output = '';
                $query = trim((string) $request->get('search'));
                if ($query) {
                    if (auth()->user()->role_id === 2) {
                        $role_id = 2;
                    } elseif (auth()->user()->role_id === 3) {
                        $role_id = 3;
                    } else {
                        $role_id = 1;
                    }
                    $normalizedQuery = str_replace([' ', '-', '_'], '', strtolower($query));

                    $menus = SmMenu::where(function ($menuQuery) use ($query, $normalizedQuery): void {
                        $menuQuery->where('name', 'LIKE', '%'.$query.'%')
                            ->orWhere('route', 'LIKE', '%'.$query.'%')
                            ->orWhereRaw("REPLACE(REPLACE(REPLACE(LOWER(name), ' ', ''), '-', ''), '_', '') LIKE ?", ['%'.$normalizedQuery.'%'])
                            ->orWhereRaw("REPLACE(REPLACE(REPLACE(LOWER(route), ' ', ''), '-', ''), '_', '') LIKE ?", ['%'.$normalizedQuery.'%']);
                    })
                        ->where('role_id', $role_id)
                        ->where('status', 1)
                        ->whereNotNull('route')
                        ->where('route', '!=', '')
                        ->get();
                    $urls = [];
                    $seenRoutes = [];
                    $routeAliases = [
                        'role_permission' => 'rolepermission/role',
                    ];

                    foreach ($menus as $menu) {
                        $routeName = $routeAliases[$menu->route] ?? $menu->route;

                        if (isset($seenRoutes[$routeName])) {
                            continue;
                        }

                        if (! empty($menu->module) && ! moduleStatusCheck($menu->module)) {
                            continue;
                        }

                        if (userPermission($routeName)) {
                            $registeredRoute = Route::getRoutes()->getByName($routeName);

                            if (! $registeredRoute || ! array_intersect(['GET', 'HEAD'], $registeredRoute->methods())) {
                                continue;
                            }

                            try {
                                $url = route($routeName);
                            } catch (\Throwable $e) {
                                // Search results must point to a registered, resolvable route.
                                continue;
                            }

                            $seenRoutes[$routeName] = true;
                            $urls[] = [
                                'name' => $menu->name,
                                'route' => $url,
                            ];
                        }
                    }

                    return response()->json($urls);
                }

                return response()->json(['not found' => 'Not Foound'], 404);

            }
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }

        return null;
    }

    public function dashboardStudentSearch(Request $request)
    {
        try {
            if (is_string($request->search)) {
                $nameOrAdmissionNo = $request->search;
            }

            if (preg_match('~\d+~', $request->search)) {
                $nameOrAdmissionNo = (int) $request->search;
            }

            return SmStudent::when(is_numeric($nameOrAdmissionNo), function ($q) use ($nameOrAdmissionNo): void {
                $q->where('admission_no', $nameOrAdmissionNo);
            })
                ->when(is_string($nameOrAdmissionNo), function ($q) use ($nameOrAdmissionNo): void {
                    $q->where('full_name', 'like', '%'.$nameOrAdmissionNo.'%');
                })
                ->get()
                ->map(function ($value): array {
                    return [
                        'name' => $value->full_name,
                        'route' => route('student_view', $value->id),
                    ];
                });
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
    }
}
