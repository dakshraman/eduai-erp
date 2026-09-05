<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmPackagePlan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'duration_days',
        'price',
        'trial_days',
        'active_status',
        'features',
        'student_quantity',
        'staff_quantity',
        'modules',
        'menus',
    ];

    protected $casts = [
        'price' => 'double',
        'modules' => 'array',
        'menus' => 'array',
        'active_status' => 'boolean',
        'duration_days' => 'integer',
        'trial_days' => 'integer',
        'student_quantity' => 'integer',
        'staff_quantity' => 'integer',
    ];

    public function subscriptionPayments()
    {
        return $this->hasMany(SmSubscriptionPayment::class, 'package_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }

    /**
     * Check if the current school has a valid subscription.
     *
     * @return bool
     */
    public static function isSubscriptionAutheticate(): bool
    {
        if (! moduleStatusCheck('Saas')) {
            return true;
        }

        $school = getSchool();
        if (! $school) {
            return false;
        }

        if ($school->id == 1) {
            return true;
        }

        $activeSubscription = SmSubscriptionPayment::where('school_id', $school->id)
            ->where('approve_status', 'approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if ($activeSubscription) {
            return true;
        }

        $trialSubscription = SmSubscriptionPayment::where('school_id', $school->id)
            ->where('payment_type', 'trial')
            ->where('approve_status', 'approved')
            ->where('end_date', '>=', now())
            ->first();

        return $trialSubscription !== null;
    }
}
