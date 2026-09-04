<?php

namespace App\Models;

use App\Scopes\GlobalAcademicScope;
use App\Scopes\StatusAcademicSchoolScope;
use App\Traits\RequestIdentityMap;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmSubject extends Model
{
    use HasFactory, RequestIdentityMap;

    protected $casts = [
        'id' => 'integer',
        'subject_name' => 'string',
        'subject_code' => 'string',
        'subject_type' => 'string',
    ];

    public function branch()
    {
        return $this->belongsTo('Modules\Branch\Entities\Branch', 'branch_id', 'id')->withDefault();
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new GlobalAcademicScope);
        static::addGlobalScope(new StatusAcademicSchoolScope);

        static::creating(function (self $subject): void {
            $subject->subject_type ??= 'T';
        });
    }

    //

}
