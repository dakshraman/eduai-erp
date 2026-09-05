<?php

namespace App\Models;

use App\Scopes\AcademicSchoolScope;
use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmVisitor extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->withDefault();
    }

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
