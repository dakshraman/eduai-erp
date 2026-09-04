<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Inventory\ItemStoreRequest;
use App\Models\SmItemStore;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class SmItemStoreController extends Controller
{
    public function index(Request $request)
    {
        /*
        try {
        */

        if (moduleStatusCheck('Branch')) {
            $itemstores = branchWise(SmItemStore::where('school_id', Auth::user()->school_id)
                ->select(['store_name', 'id', 'store_no', 'description', 'branch_id'])
                ->get());
        } else {
            $itemstores = SmItemStore::where('school_id', Auth::user()->school_id)
                ->select(['store_name', 'id', 'store_no', 'description'])
                ->get();
        }

        return view('backEnd.inventory.itemStoreList', ['itemstores' => $itemstores]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function store(ItemStoreRequest $itemStoreRequest)
    {
        /*
        try {
        */
        $smItemStore = new SmItemStore();
        $smItemStore->store_name = $itemStoreRequest->store_name;
        $smItemStore->store_no = $itemStoreRequest->store_no;
        $smItemStore->description = $itemStoreRequest->description;
        $smItemStore->school_id = Auth::user()->school_id;
        if (moduleStatusCheck('Branch')) {
            $smItemStore->branch_id = $itemStoreRequest->branch_id;
        }
        if (moduleStatusCheck('University')) {
            $smItemStore->un_academic_id = getAcademicId();
        } else {
            $smItemStore->academic_id = getAcademicId();
        }
        if (Schema::hasColumn('sm_item_stores', 'un_academic_id')) {
            $smItemStore->un_academic_id = getAcademicId();
        }

        $smItemStore->save();

        Toastr::success('Operation successful', 'Success');

        return redirect()->back();
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function edit(Request $request, $id)
    {
        /*
        try {
        */
        $editData = SmItemStore::find($id);
        $itemstores = branchWise(SmItemStore::where('school_id', Auth::user()->school_id)->get());

        return view('backEnd.inventory.itemStoreList', ['editData' => $editData, 'itemstores' => $itemstores]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function update(ItemStoreRequest $itemStoreRequest, $id)
    {
        /*
        try {
        */
        $stores = SmItemStore::find($id);
        $stores->store_name = $itemStoreRequest->store_name;
        $stores->store_no = $itemStoreRequest->store_no;
        $stores->description = $itemStoreRequest->description;
        if (moduleStatusCheck('Branch')) {
            $stores->branch_id = $itemStoreRequest->branch_id;
        }
        if (moduleStatusCheck('University')) {
            $stores->un_academic_id = getAcademicId();
        }
        if (Schema::hasColumn('sm_item_stores', 'un_academic_id')) {
            $stores->un_academic_id = getAcademicId();
        }
        $stores->update();
        Toastr::success('Operation successful', 'Success');

        return redirect('item-store');
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function deleteStoreView(Request $request, $id)
    {
        /*
        try {
        */
        $title = __('inventory.delete_store');
        $url = route('delete-store', $id);

        return view('backEnd.modal.delete', ['id' => $id, 'title' => $title, 'url' => $url]);
        /*
        } catch (Exception $exception) {
            Toastr::error('Operation Failed', 'Failed');

            return redirect()->back();
        }
        */
    }

    public function deleteStore(Request $request, $id)
    {
        /*
        try {
        */
        $tables = \App\Support\tableList::getTableList('store_id', $id);
        /*
        try {
        */
        if ($tables === null || $tables== '') {
            SmItemStore::destroy($id);
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
        }
    } catch (Exception $exception) {
        Toastr::error('Operation Failed', 'Failed');

        return redirect()->back();
    }
    */
    }
}
