<?php

namespace Modules\RolePermission\Entities;

use Illuminate\Database\Eloquent\Model;

class InfixPermissionAssign extends Model
{
    protected $casts = [
        'saas_schools' => 'array',
    ];

    protected $fillable = [];

    public function routeName()
    {
        return $this->belongsTo(InfixModuleInfo::class, 'module_id', 'id');
    }
}
