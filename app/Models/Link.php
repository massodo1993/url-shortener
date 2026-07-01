<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Sqids\Sqids;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_url',
        'code',
        'clicks_count',
    ];

    protected static function booted(): void
    {
        static::creating(function (Link $link) {
            if (empty($link->code)) {
                $link->id = DB::selectOne(
                    "select nextval(pg_get_serial_sequence('links', 'id')) as id"
                )->id;

                $link->code = app(Sqids::class)->encode([$link->id]);
            }
        });
    }

    public function getShortUrlAttribute(): string
    {
        return url('/' . $this->code);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class);
    }
}
