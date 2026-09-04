<?php

namespace App\Models;

use App\Scopes\AcademicSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamMeritPosition extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'total_mark' => 'float',
        'gpa' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new AcademicSchoolScope);
        static::creating(function (self $position): void {
            $position->total_mark ??= 0;
        });
    }
}
