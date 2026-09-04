<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmVehicle extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'vehicle_model' => 'string',
        'vehicle_no' => 'string',
        'made_year' => 'integer',
        'note' => 'string',
    ];

    public static function findVehicle($id)
    {
        try {
            return self::find($id);
        } catch (Exception $exception) {
            return [];
        }
    }
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new ActiveStatusSchoolScope);
    // }

    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class, 'branch_id', 'id');
    }

    public function driver()
    {
        return $this->belongsTo(SmStaff::class, 'driver_id', 'id');
    }

    public function scopeStatus($query)
    {
        return $query->where('school_id', auth()->user()->id);
    }
}
