<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    public function getShiftNameAttribute()
    {
        return $this->name;
    }

    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class, 'branch_id', 'id')->withDefault();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            cache()->forget('shifts');
        });

        static::updating(function ($model) {
            cache()->forget('shifts');
        });
    }
}
