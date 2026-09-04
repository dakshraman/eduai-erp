<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmSubscriptionPayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'package_id',
        'payment_type',
        'approve_status',
        'bank_name',
        'account_holder',
        'payment_date',
        'payment_method',
        'file',
        'amount',
        'school_id',
        'start_date',
        'end_date',
        'buy_type',
    ];

    protected $casts = [
        'amount' => 'double',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $dates = ['start_date', 'end_date', 'payment_date'];

    public function school()
    {
        return $this->belongsTo(\App\Models\SmSchool::class, 'school_id');
    }

    public function package()
    {
        return $this->belongsTo(SmPackagePlan::class, 'package_id');
    }

    public function scopeActive($query)
    {
        return $query->where('approve_status', 'approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('approve_status', $status);
    }

    public function scopeForSchool($query, int $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}
