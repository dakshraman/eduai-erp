<?php

namespace App\Models;

use App\Scopes\AcademicSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmFeesGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'created_by', 'active_status', 'school_id', 'un_semester_label_id', 'un_subject_id', 'un_academic_id', 'branch_id'];

    public function feesMasters()
    {
        return $this->hasmany(SmFeesMaster::class, 'fees_group_id');
    }

    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class, 'branch_id', 'id')->withDefault();
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new AcademicSchoolScope);
    }
}
