<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomSmsSetting extends Model
{
    use HasFactory;

    public function smsGateway()
    {
        return $this->belongsTo(SmSmsGateway::class, 'gateway_id', 'id');
    }
}
