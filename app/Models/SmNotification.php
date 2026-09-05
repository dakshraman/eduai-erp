<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmNotification extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::saved(function (SmNotification $notification): void {
            self::clearUnreadCache($notification->user_id, $notification->role_id, $notification->academic_id);
        });

        static::deleted(function (SmNotification $notification): void {
            self::clearUnreadCache($notification->user_id, $notification->role_id, $notification->academic_id);
        });
    }

    public static function notifications()
    {
        $user = Auth()->user();
        if ($user) {
            $cacheKey = self::unreadCacheKey($user->id, $user->role_id, getAcademicId());

            return Cache::remember($cacheKey, 30, function () use ($user) {
                return self::where('user_id', $user->id)
                    ->where('role_id', $user->role_id)
                    ->where('academic_id', getAcademicId())
                    ->where('is_read', 0)
                    ->latest()
                    ->get();
            });
        }

        return null;

    }

    public static function clearUnreadCache($userId = null, $roleId = null, $academicId = null): void
    {
        $userId = $userId ?: auth()->id();
        $roleId = $roleId ?: auth()->user()?->role_id;
        $academicId = $academicId ?: getAcademicId();

        if (! $userId || ! $roleId || ! $academicId) {
            return;
        }

        Cache::forget(self::unreadCacheKey($userId, $roleId, $academicId));
    }

    public static function insertAndClearUnreadCache(array $records): bool
    {
        if (empty($records)) {
            return true;
        }

        $records = isset($records['user_id']) ? [$records] : $records;
        $inserted = self::insert($records);

        if ($inserted) {
            $clearedKeys = [];
            foreach ($records as $record) {
                $userId = $record['user_id'] ?? null;
                $roleId = $record['role_id'] ?? null;
                $academicId = $record['academic_id'] ?? getAcademicId();

                if (! $userId || ! $roleId || ! $academicId) {
                    continue;
                }

                $cacheKey = self::unreadCacheKey($userId, $roleId, $academicId);
                if (isset($clearedKeys[$cacheKey])) {
                    continue;
                }

                Cache::forget($cacheKey);
                $clearedKeys[$cacheKey] = true;
            }
        }

        return $inserted;
    }

    private static function unreadCacheKey($userId, $roleId, $academicId): string
    {
        return 'unread_notifications_user_'.$userId.'_role_'.$roleId.'_academic_'.$academicId;
    }
}
