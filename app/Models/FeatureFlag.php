<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FeatureFlag extends Model
{
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['name', 'is_active', 'description', 'created_at'];

    protected $casts = [
        'is_active'  => 'boolean',
        'created_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn ($m) => $m->created_at = now());
    }

    public static function isActive(string $name): bool
    {
        return Cache::remember("feature_flag:{$name}", 300, function () use ($name) {
            return static::where('name', $name)->value('is_active') ?? false;
        });
    }

    public static function enable(string $name): void
    {
        static::where('name', $name)->update(['is_active' => true]);
        Cache::forget("feature_flag:{$name}");
    }

    public static function disable(string $name): void
    {
        static::where('name', $name)->update(['is_active' => false]);
        Cache::forget("feature_flag:{$name}");
    }
}
