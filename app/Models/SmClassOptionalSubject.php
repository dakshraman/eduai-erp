<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmClassOptionalSubject extends Model
{
    // protected $fillable = [];
    protected $table = 'sm_class_optional_subject';

    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class, 'branch_id', 'id');
    }
}
