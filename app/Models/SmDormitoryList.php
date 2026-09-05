<?php

namespace App\Models;

use App\Scopes\ActiveStatusSchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmDormitoryList extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'dormitory_name' => 'string',
    ];

    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class, 'branch_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ActiveStatusSchoolScope);
    }
}
