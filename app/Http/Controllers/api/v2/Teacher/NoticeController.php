<?php

namespace App\Http\Controllers\api\v2\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SmNoticeBoard;

class NoticeController extends Controller
{
    public function noticeList()
    {
        auth()->user()->roles;

        $data = SmNoticeBoard::where('inform_to', 'like', '%"4"%')
            ->where('school_id', auth()->user()->school_id)
            ->where('publish_on', '<=', date('Y-m-d'))
            ->orderBy('id', 'DESC')
            ->get(['id', 'notice_title', 'notice_message', 'notice_date']);

        return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Notice list',
            ]);
    }
}
