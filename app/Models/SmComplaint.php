<?php

namespace App\Models;

use App\Scopes\StatusAcademicSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmComplaint extends Model
{
    use HasFactory;

    public function complaintType()
    {
        return $this->belongsTo(SmSetupAdmin::class, 'complaint_type', 'id');
    }

    public function complaintSource()
    {
        return $this->belongsTo(SmSetupAdmin::class, 'complaint_source', 'id');
    }

    public function branch()
    {
        return $this->belongsTo('Modules\Branch\Entities\Branch', 'branch_id', 'id')->withDefault();
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new StatusAcademicSchoolScope);
    }
}
