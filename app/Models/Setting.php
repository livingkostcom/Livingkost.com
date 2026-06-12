<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    /**
     * Get a setting value by key, with a default fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget("setting.{$key}");
    }

    /**
     * Get all settings as key-value array, optionally filtered by group.
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Default values for all settings.
     */
    public static function defaults(): array
    {
        return [
            // General
            'app_name' => 'Fluty Kos',
            'app_tagline' => 'Sistem Manajemen Kos',
            'app_address' => '',
            'app_phone' => '',
            'app_email' => '',

            // Payment
            'bank_name' => '',
            'bank_account_number' => '',
            'bank_account_holder' => '',
            'payment_instructions' => '',

            // Late Fee
            'late_fee_enabled' => '0',
            'late_fee_type' => 'fixed',
            'late_fee_amount' => '0',
            'late_fee_grace_days' => '3',
        ];
    }

    /**
     * Get setting with default fallback from defaults().
     */
    public static function getValue(string $key): mixed
    {
        $defaults = static::defaults();
        return static::get($key, $defaults[$key] ?? null);
    }
}
