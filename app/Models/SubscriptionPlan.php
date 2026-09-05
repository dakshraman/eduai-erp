<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'features' => 'array',
        'active_status' => 'boolean',
        'price_cents' => 'integer',
        'max_students' => 'integer',
        'max_teachers' => 'integer',
        'display_order' => 'integer',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(SchoolSubscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    public function getPriceAttribute(): string
    {
        return number_format($this->price_cents / 100, 2);
    }
}
