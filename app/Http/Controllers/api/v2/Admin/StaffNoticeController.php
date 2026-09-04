<?php

namespace App\Http\Controllers\api\v2\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmNoticeBoard;

class StaffNoticeController extends Controller
{
    public function noticeList()
    {
        $allNotices = SmNoticeBoard::where('inform_to', 'like', '%"5"%')
            ->where('school_id', auth()->user()->school_id)
            ->where('publish_on', '<=', date('Y-m-d'))
            ->orderBy('id', 'DESC')
            ->get(['id', 'notice_title', 'notice_message', 'notice_date']);


        return response()->json([
            'success' => true,
            'data' => $allNotices,
            'message' => 'Notice list successful',
        ]);
    }
}
