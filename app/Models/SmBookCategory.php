<?php

namespace App\Models;

use App\Scopes\AcademicSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SmBookCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'school_id',
        'academic_id',
        'un_academic_id',
        'branch_id'
    ];

    protected $casts = [
        'id' => 'integer',
        'category_name' => 'string',
    ];

    public function scopeStatus($query)
    {
        return $query->where('school_id', Auth::check() ? Auth::user()->school_id : 1);
    }

    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class, 'branch_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        
        // Don't apply global scope in test environment
        if (app()->environment() !== 'testing') {
            static::addGlobalScope(new AcademicSchoolScope);
        }
    }
}
