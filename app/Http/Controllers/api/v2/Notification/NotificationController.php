<?php

namespace App\Http\Controllers\api\v2\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\SmNotificationResource;
use App\Models\SmNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function allNotificationList()
    {
        $notifications = SmNotification::orderBy('id', 'DESC')
            ->where('user_id', auth()->user()->id)
            ->where('role_id', auth()->user()->role_id)
            ->where('school_id', auth()->user()->school_id)
            ->where('is_read', 0)
            ->get();

        $data['unread_notifications_count'] = (int) @$notifications->count();
        $data['unread_notifications'] = SmNotificationResource::collection(@$notifications);
        return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Unread notification list',
            ]);
    }

    public function allNotificationMarkRead()
    {
        $user = Auth()->user();
        if (Auth()->user()->role_id !== 1) {
            if ($user->role_id === 2) {
                $data = SmNotification::where('user_id', Auth::user()->id)->where('school_id', auth()->user()->school_id)->where('role_id', 2)->update(['is_read' => 1]);
            } elseif ($user->role_id === 3) {
                $data = SmNotification::where('user_id', Auth::user()->id)->where('school_id', auth()->user()->school_id)->where('role_id', '!=', 2)->update(['is_read' => 1]);
            } else {
                $data = SmNotification::where('user_id', $user->id)->where('school_id', auth()->user()->school_id)->where('role_id', '!=', 2)->where('role_id', '!=', 3)->update(['is_read' => 1]);
            }
        } else {
            $data = SmNotification::where('user_id', $user->id)->where('school_id', auth()->user()->school_id)->where('role_id', 1)->update(['is_read' => 1]);
        }

        SmNotification::clearUnreadCache($user->id, $user->role_id);


        return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'All notification marked as read',
            ]);
    }
}
