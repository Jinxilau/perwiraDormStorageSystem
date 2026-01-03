<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'boolean',
    ];


    public static function get($key, $default = null)
    {
        // Fetch from cache/database(if not exist)
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default; // This runs only when they key doesn't exist
        });
    }

    /**
     * Update or create setting value.
     */
    public static function set($key, $value)
    {
        // Update/Create new value to setting database
        static::updateOrCreate(['key' => $key], ['value' => $value]);

        // Forget the old cache
        Cache::forget("setting_{$key}");
    }
}
