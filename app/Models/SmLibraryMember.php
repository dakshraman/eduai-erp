<?php

namespace App\Models;

use App\Scopes\StatusAcademicSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmLibraryMember extends Model
{
    use HasFactory;

    public function roles()
    {
        return $this->belongsTo(\Modules\RolePermission\Entities\InfixRole::class, 'member_type', 'id');
    }

    public function studentDetails()
    {
        return $this->belongsTo(SmStudent::class, 'student_staff_id', 'user_id');
    }

    public function staffDetails()
    {
        return $this->belongsTo(SmStaff::class, 'student_staff_id', 'user_id');
    }

    public function parentsDetails()
    {
        return $this->belongsTo(SmParent::class, 'student_staff_id', 'user_id');
    }

    public function memberTypes()
    {
        return $this->belongsTo(\Modules\RolePermission\Entities\InfixRole::class, 'member_type', 'id');
    }

    public function scopeStatus($query)
    {
        $query->where('school_id', auth()->user()->school_id)->where('academic_id', getAcademicId());
    }

    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class, 'branch_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new StatusAcademicSchoolScope);
    }
}
