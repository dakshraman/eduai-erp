<?php

namespace App\Http\Controllers\api\v2\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmAcademicYear;
use App\Models\SmAssignVehicle;
use App\Models\SmRoute;
use App\Models\SmVehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssignVehicleController extends Controller
{
    public function assignToRoute()
    {
        $data['vehicles'] = SmVehicle::where('school_id', auth()->user()->school_id)->select('id', 'vehicle_no')->get();
        $data['routes'] = SmRoute::where('school_id', auth()->user()->school_id)->select('id', 'title')->get();


        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Vehicle & route list',
        ]);
    }

    public function storeAssign(Request $request)
    {
        $this->validate($request, [
            'route' => [
                'required',
                Rule::exists('sm_routes', 'id')->where('school_id', auth()->user()->school_id),
                Rule::unique('sm_assign_vehicles', 'route_id')->where('school_id', auth()->user()->school_id),
            ],
            'vehicles' => 'required|exists:sm_vehicles,id',
        ]);

        $smAssignVehicle = new SmAssignVehicle();
        $smAssignVehicle->route_id = $request->route;
        $smAssignVehicle->vehicle_id = $request->vehicles;
        $smAssignVehicle->school_id = auth()->user()->school_id;
        $smAssignVehicle->academic_id = SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR();
        $smAssignVehicle->save();

        return response()->json( [
            'success' => true,
            'data' => null,
            'message' => 'The Vehicle assigned for the route successfully',
        ]);
    }

    public function assignList()
    {
        $assignList = SmAssignVehicle::with('route', 'vehicle')
            ->where('school_id', auth()->user()->school_id)
            ->get()
            ->map(function ($assign): array {
                return [
                    'id' => (int) $assign->id,
                    'route_name' => (string) @$assign->route->title,
                    'vehicle_no' => (string) @$assign->vehicle->vehicle_no,
                    'made_year' => (int) @$assign->vehicle->made_year,
                    'vehicle_model' => (string) @$assign->vehicle->vehicle_model,
                    'driver_name' => (string) @$assign->vehicle->driver->full_name,
                    'driving_license' => (string) @$assign->vehicle->driver->driving_license,
                    'driver_contact_no' => (string) @$assign->vehicle->driver->mobile,
                ];
            });


        return response()->json([
            'success' => true,
            'data' => $assignList,
            'message' => 'Assigned vehicle list',
        ]);
    }
}
