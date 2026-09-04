<?php

namespace App\Models;

use App\Scopes\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Theme extends Model
{
    use HasFactory;

    protected static function booted(): void
    {


        static::updating(function (Theme $theme) {
            Cache::forget('color_themes_all');
            Cache::forget('color_themes_user_'.auth()->id());
        });
        static::creating(function (Theme $theme) {
            Cache::forget('color_themes_all');
            Cache::forget('color_themes_user_'.auth()->id());
        });


    }

    protected $guarded = [];

    public function colors()
    {
        return $this->belongsToMany(Color::class)->where('status', 1)->withPivot(['value']);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new SchoolScope);
    }
}
