<?php

namespace App\Models;

use App\Scopes\AcademicSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmPostalReceive extends Model
{
    //
    use HasFactory;

    public function branch()
    {
        return $this->belongsTo('Modules\Branch\Entities\Branch', 'branch_id', 'id')->withDefault();
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new AcademicSchoolScope);
    }
}
