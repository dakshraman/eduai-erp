<?php

namespace App\Http\Controllers\api\v2\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\v2\Admin\DormitoryRoomListResource;
use App\Models\SmAcademicYear;
use App\Models\SmDormitoryList;
use App\Models\SmRoomList;
use App\Models\SmRoomType;
use App\Scopes\ActiveStatusSchoolScope;
use Illuminate\Http\Request;

class DormitoryController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'dormitory_name' => 'required|max:200|unique:sm_dormitory_lists',
            'type' => 'required',
            'address' => 'required',
            'intake' => 'required',
            'description' => 'sometimes|nullable|max:200',
        ]);

        $smDormitoryList = new SmDormitoryList();
        $smDormitoryList->dormitory_name = $request->dormitory_name;
        $smDormitoryList->type = $request->type;
        $smDormitoryList->address = $request->address;
        $smDormitoryList->intake = $request->intake;
        $smDormitoryList->description = $request->description;
        $smDormitoryList->school_id = auth()->user()->school_id;
        if (moduleStatusCheck('University')) {
            $smDormitoryList->un_academic_id = SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR();
        } else {
            $smDormitoryList->academic_id = SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR();
        }

        $smDormitoryList->save();
        if (! $smDormitoryList) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Operation failed',
            ]);
        }

        return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Dormitory stored successfully',
            ]);
    }

    public function index()
    {
        $room_lists = SmRoomList::withoutGlobalScope(ActiveStatusSchoolScope::class)
            ->with('dormitory', 'roomType')
            ->where('school_id', auth()->user()->school_id)
            ->get();

        $anonymousResourceCollection = DormitoryRoomListResource::collection($room_lists);


        return response()->json([
            'success' => true,
            'data' => $anonymousResourceCollection,
            'message' => 'Dormitory room list',
        ]);
    }

    public function dormitoryRoomStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:100',
            'dormitory_id' => 'required|integer',
            'room_type_id' => 'required|integer',
            'number_of_bed' => 'required|max:2',
            'cost_per_bed' => 'required|max:11',
            'description' => 'sometimes|nullable|max:200',
        ]);

        $smRoomList = new SmRoomList();
        $smRoomList->name = $request->name;
        $smRoomList->dormitory_id = $request->dormitory_id;
        $smRoomList->room_type_id = $request->room_type_id;
        $smRoomList->number_of_bed = $request->number_of_bed;
        $smRoomList->cost_per_bed = $request->cost_per_bed;
        $smRoomList->description = $request->description;
        $smRoomList->school_id = auth()->user()->school_id;
        if (moduleStatusCheck('University')) {
            $smRoomList->un_academic_id = SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR();
        } else {
            $smRoomList->academic_id = SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR();
        }

        $smRoomList->save();

        return response()->json( [
            'success' => true,
            'data' => null,
            'message' => 'Dormitory room stored successfully',
        ]);
    }

    public function roomType()
    {
        $data = SmRoomType::withoutGlobalScope(ActiveStatusSchoolScope::class)
            ->where('school_id', auth()->user()->school_id)
            ->select('id', 'type')
            ->get();


        return response()->json( [
            'success' => true,
            'data' => $data,
            'message' => 'Room type list',
        ]);
    }

    public function dormitoryList()
    {
        $data = SmDormitoryList::withoutGlobalScope(ActiveStatusSchoolScope::class)
            ->where('school_id', auth()->user()->school_id)
            ->select('id', 'dormitory_name')
            ->get();



        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Dormitory list',
        ]);
    }
}
