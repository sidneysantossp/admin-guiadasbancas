<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ForbiddenWord extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saved(function () {
            Cache::forget('forbidden_words_all');
        });
        static::deleted(function () {
            Cache::forget('forbidden_words_all');
        });
    }
}