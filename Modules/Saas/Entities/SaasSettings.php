<?php

namespace Modules\Saas\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaasSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'lang_name',
        'active_status',
        'saas_status',
        'infix_module_id',
        'user_id',
        'route',
    ];

    protected $casts = [
        'active_status' => 'boolean',
        'saas_status' => 'boolean',
    ];

    public function scopeByKey($query, string $key)
    {
        return $query->where('route', $key);
    }

    /**
     * Get a setting value by route key.
     *
     * @param string $key
     * @return static|null
     */
    public static function getSetting(string $key)
    {
        return static::where('route', $key)->first();
    }

    /**
     * Set a setting value.
     *
     * @param string $key
     * @param string $langName
     * @param bool $saasStatus
     * @return static
     */
    public static function setSetting(string $key, string $langName = '', bool $saasStatus = true): static
    {
        return static::updateOrCreate(
            ['route' => $key],
            [
                'lang_name' => $langName,
                'saas_status' => $saasStatus,
                'active_status' => true,
            ]
        );
    }
}
