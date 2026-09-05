<?php

namespace App\Http\Controllers\Admin\Transport;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Transport\SmRouteRequest;
use App\Models\SmRoute;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmRouteController extends Controller
{
    public function index(Request $request)
    {
        /*
        try {
        */
        $routes = branchWise(SmRoute::where('school_id', Auth::user()->school_id)->get());

        return view('backEnd.transport.route', ['routes' => $routes]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function store(SmRouteRequest $smRouteRequest)
    {
        /*
        try {
        */
        $smRoute = new SmRoute();
        $smRoute->title = $smRouteRequest->title;
        $smRoute->far = (float) round($smRouteRequest->far, getDecimalDigit());
        $smRoute->school_id = Auth::user()->school_id;
        if (moduleStatusCheck('University')) {
            $smRoute->un_academic_id = getAcademicId();
        } else {
            $smRoute->academic_id = getAcademicId();
        }

        if (moduleStatusCheck('Branch')) {
            $smRoute->branch_id = $smRouteRequest->branch_id;
        }

        $smRoute->save();

        Toastr::success('Operation successful', 'Success');

        return redirect()->back();
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function show(Request $request, $id)
    {
        /*
        try {
        */
        $route = SmRoute::find($id);
        $routes = branchWise(SmRoute::where('school_id', Auth::user()->school_id)->get());

        return view('backEnd.transport.route', ['route' => $route, 'routes' => $routes]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function update(SmRouteRequest $smRouteRequest, $id)
    {
        /*
        try {
        */
        $route = SmRoute::find($smRouteRequest->id);
        $route->title = $smRouteRequest->title;
        if (moduleStatusCheck('Branch')) {
            $route->branch_id = $smRouteRequest->branch_id;
        }
        $route->far = (float) round($smRouteRequest->far, getDecimalDigit());
        if (moduleStatusCheck('University')) {
            $route->un_academic_id = getAcademicId();
        }

        $route->save();

        Toastr::success('Operation successful', 'Success');

        return redirect('transport-route');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function destroy(Request $request, $id)
    {
        /*
        try {
        */
        $tables = \App\Support\tableList::getTableList('route_id', $id);
        /*
        try {
        */
        if ($tables === null || $tables== '') {
            SmRoute::destroy($id);

            Toastr::success('Operation successful', 'Success');

            return redirect()->back();
        }
        $msg = 'This data already used in  : '.$tables.' Please remove those data first';
        Toastr::error($msg, 'Failed');

        return redirect()->back();

        /*
        } catch (\Illuminate\Database\QueryException $e) {
            $msg = 'This data already used in  : '.$tables.' Please remove those data first';
            Toastr::error($msg, 'Failed');

            return redirect()->back();
        } catch (Exception $e) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        } catch (Exception $exception) {
        Toastr::error('Operation Failed', 'Failed');

        return redirect()->back();
        }
        */
    }
}
