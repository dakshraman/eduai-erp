<?php

namespace Modules\DownloadCenter\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected static function booted(): void
    {
        static::creating(function (self $content): void {
            // Keep legacy callers that construct Content directly compatible
            // with the non-null columns used by the download-center schema.
            $content->content_type_id ??= 1;
            $content->uploaded_by ??= auth()->id() ?? 1;
        });
    }

    public function contentType()
    {
        return $this->belongsTo(ContentType::class, 'content_type_id', 'id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id')->withDefault();
    }

    public function branch()
    {
        return $this->belongsTo(\Modules\Branch\Entities\Branch::class, 'branch_id', 'id')->withDefault();
    }
}
