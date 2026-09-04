<?php

namespace Modules\RolePermission\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignPermission extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'permission_id', 'status', 'menu_status', 'saas_schools', 'role_id', 'school_id'];

    protected $table = 'assign_permissions';

    public function permissionInfo()
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function (AssignPermission $assignPermission): void {
            self::clearPermissionMenuCache($assignPermission);
        });

        static::deleted(function (AssignPermission $assignPermission): void {
            self::clearPermissionMenuCache($assignPermission);
        });
    }

    private static function clearPermissionMenuCache(AssignPermission $assignPermission): void
    {
        if (function_exists('clearPermissionMenuCache')) {
            clearPermissionMenuCache($assignPermission->school_id);
        }
    }

    protected static function newFactory()
    {
        return \Modules\RolePermission\Database\factories\AssignPermissionFactory::new();
    }
}
