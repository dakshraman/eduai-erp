<?php

namespace App\Http\Controllers\Admin\Dormitory;

use App\Http\Controllers\Controller;
use App\Models\SmRoomType;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SmRoomTypeController extends Controller
{
    public function index(Request $request)
    {
        /*
        try {
        */
        $room_types = branchWise(SmRoomType::get());

        return view('backEnd.dormitory.room_type', ['room_types' => $room_types]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $rules = [
            'type' => 'required',
        ];

        if (moduleStatusCheck('Branch')) {
            $rules['branch_id'] = 'required';
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        /*
        try {
        */
        $smRoomType = new SmRoomType();
        $smRoomType->type = $request->type;
        $smRoomType->description = $request->description;
        $smRoomType->school_id = Auth::user()->school_id;
        if (moduleStatusCheck('University')) {
            $smRoomType->un_academic_id = getAcademicId();
        } else {
            $smRoomType->academic_id = getAcademicId();
        }

        if (moduleStatusCheck('Branch')) {
            $smRoomType->branch_id = $request->branch_id;
        }

        $smRoomType->save();

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
        $room_type = SmRoomType::find($id);
        $room_types = branchWise(SmRoomType::where('school_id', Auth::user()->school_id)->get());

        return view('backEnd.dormitory.room_type', ['room_types' => $room_types, 'room_type' => $room_type]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function update(Request $request, $id)
    {
        /*
        try {
        */
        $room_type = SmRoomType::find($request->id);
        $room_type->type = $request->type;
        $room_type->description = $request->description;
        if (moduleStatusCheck('University')) {
            $room_type->un_academic_id = getAcademicId();
        }

        if (moduleStatusCheck('Branch')) {
            $room_type->branch_id = $request->branch_id;
        }

        $room_type->save();

        Toastr::success('Operation successful', 'Success');

        return redirect('room-type');
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
        $tables = \App\Support\tableList::getTableList('room_type_id', $id);
        /*
        try {
        */
        if ($tables === null || $tables== '') {
            SmRoomType::destroy($id);

            Toastr::success('Operation successful', 'Success');

            return redirect('room-type');
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
