<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmDonor extends Model
{
    use HasFactory;

    public function gender()
    {
        return $this->belongsTo(SmBaseSetup::class, 'gender_id', 'id')->withDefault();
    }

    public function religion()
    {
        return $this->belongsTo(SmBaseSetup::class, 'religion_id', 'id')->withDefault();
    }

    public function bloodGroup()
    {
        return $this->belongsTo(SmBaseSetup::class, 'bloodgroup_id', 'id')->withDefault();
    }
}
