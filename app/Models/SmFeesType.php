<?php

namespace App\Models;

use App\Scopes\StatusAcademicSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmFeesType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'fees_group_id', 'un_semester_label_id', 'school_id', 'un_subject_id', 'un_academic_id'];

    public function fessGroup()
    {
        return $this->belongsTo(SmFeesGroup::class, 'fees_group_id');
    }

    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class, 'branch_id', 'id')->withDefault();
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new StatusAcademicSchoolScope);
    }
}
