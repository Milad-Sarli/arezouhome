<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, ?string $value): void
    {
        if ($key === 'about' && $value) {
            $value = strip_tags($value, '<p><br><strong><em><ul><ol><li><h2><h3><a><span>');
            $value = preg_replace('/href\s*=\s*["\']\s*javascript:/i', 'href="#"', $value);
        }

        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::flush());
        static::deleted(fn () => Cache::flush());
    }
}
