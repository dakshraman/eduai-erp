<?php

namespace App\Http\Controllers\api\v2\NoticeBoard;

use App\Http\Controllers\Controller;
use App\Models\SmAcademicYear;
use App\Models\SmNoticeBoard;
use App\Scopes\StatusAcademicSchoolScope;
use Illuminate\Http\Request;

class NoticeBoardController extends Controller
{
    public function studentNoticeboard(Request $request)
    {
        $data = [];
        if (auth()->user()->role_id === 2) {
            $data['allNotices'] = SmNoticeBoard::withoutGlobalScopes([StatusAcademicSchoolScope::class])
                ->select('id', 'notice_title', 'notice_message', 'publish_on')
                ->where('active_status', 1)
                ->where('inform_to', 'LIKE', '%2%')
                ->orderBy('id', 'DESC')
                ->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())
                ->where('school_id', auth()->user()->school_id)
                ->get();
        } elseif (auth()->user()->role_id === 3) {
            $data['allNotices'] = SmNoticeBoard::withoutGlobalScopes([StatusAcademicSchoolScope::class])
                ->select('id', 'notice_title', 'notice_message', 'publish_on')
                ->where('active_status', 1)
                ->where('inform_to', 'LIKE', '%3%')
                ->orderBy('id', 'DESC')
                ->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())
                ->where('school_id', auth()->user()->school_id)
                ->get();
        }

        return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Notice board list',
            ]);
    }

    public function studentSingleNoticeboard(Request $request)
    {
        $data = [];

        if (auth()->user()->role_id === 2) {
            $data = SmNoticeBoard::withoutGlobalScopes([StatusAcademicSchoolScope::class])
                ->select('id', 'notice_title', 'notice_message', 'publish_on')
                ->where('active_status', 1)
                ->where('inform_to', 'LIKE', '%2%')
                ->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())
                ->where('school_id', auth()->user()->school_id)
                ->findOrFail($request->notice_board_id);
        } elseif (auth()->user()->role_id === 3) {
            $data = SmNoticeBoard::withoutGlobalScopes([StatusAcademicSchoolScope::class])
                ->select('id', 'notice_title', 'notice_message', 'publish_on')
                ->where('active_status', 1)
                ->where('inform_to', 'LIKE', '%3%')
                ->where('academic_id', SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR())
                ->where('school_id', auth()->user()->school_id)
                ->findOrFail($request->notice_board_id);
        }

        return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'View single notice',
            ]);
    }
}
