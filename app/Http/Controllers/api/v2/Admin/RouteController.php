<?php

namespace App\Http\Controllers\api\v2\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmAcademicYear;
use App\Models\SmRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RouteController extends Controller
{
    public function routeList()
    {
        $routes = SmRoute::where('school_id', auth()->user()->school_id)->select('id', 'title', 'far')->get();


        return response()->json( [
            'success' => true,
            'data' => $routes,
            'message' => 'Route list successful',
        ]);
    }

    public function storeRoute(Request $request)
    {
        $this->validate($request, [
            'title' => ['required', 'max:200', Rule::unique('sm_routes', 'title')->where('school_id', auth()->user()->school_id)],
            'far' => 'required|numeric',
        ]);

        $smRoute = new SmRoute();
        $smRoute->title = $request->title;
        $smRoute->far = $request->far;
        $smRoute->school_id = Auth::user()->school_id;
        $smRoute->academic_id = SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR();
        $smRoute->save();

        $data = SmRoute::select('id', 'title', 'far')->find($smRoute->id);


        return response()->json( [
            'success' => true,
            'data' => [$data],
            'message' => 'The route created successfully',
        ]);
    }

    public function updateRoute(Request $request)
    {
        $school_id = auth()->user()->school_id;
        $this->validate($request, [
            'route_id' => ['required', Rule::exists('sm_routes', 'id')->where('school_id', $school_id)],
            'title' => ['required', 'max:200', Rule::unique('sm_routes', 'title')->where('school_id', $school_id)->ignore($request->route_id)],
            'far' => 'required|numeric',
        ], [
            'route_id.exists' => 'Invalid route',
        ]);

        $route = SmRoute::where('school_id', auth()->user()->school_id)->where('id', $request->route_id)->first();
        $route->title = $request->title;
        $route->far = $request->far;
        $route->save();

        $data = SmRoute::select('id', 'title', 'far')->find($route->id);


        return response()->json( [
            'success' => true,
            'data' => [$data],
            'message' => 'The route updated successfully',
        ]);
    }

    public function deleteRoute(Request $request)
    {

        $this->validate($request, [
            'route_id' => ['required', Rule::exists('sm_routes', 'id')->where('school_id', auth()->user()->school_id)],
        ], [
            'route_id.exists' => 'Invalid route',
        ]);

        $id = $request->route_id;

        $tables = \App\Support\tableList::getTableList('route_id', $id);

        $delete = null;
        if ($tables === null || !$tables === '') {
            $delete = SmRoute::where('school_id', auth()->user()->school_id)->where('id', $id)->delete();
        } else {
            $msg = 'This data already used in  : '.$tables.' Please remove those data first';
        }

        if (! $delete) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => $msg,
            ]);
        }

        return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'The route deleted successfully',
            ]);
    }
}
