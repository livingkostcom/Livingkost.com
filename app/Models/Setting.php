<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'owner_id'];

    /**
     * Keys that are platform-wide (shared across all owners), e.g. branding.
     * Everything else is scoped per owner with a fallback to the global value.
     */
    public const GLOBAL_KEYS = ['app_name', 'app_tagline'];

    /**
     * The owner scope for settings: null for global keys / unauthenticated /
     * super-admin contexts; otherwise the current user's owner id.
     */
    protected static function ownerScope(string $key): ?int
    {
        if (in_array($key, self::GLOBAL_KEYS, true)) {
            return null;
        }

        return Auth::check() ? Auth::user()->ownerId() : null;
    }

    /**
     * Get a setting value by key, with a default fallback.
     * Owner-scoped keys fall back to the global value when no override exists.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $ownerId = static::ownerScope($key);
        $cacheKey = "setting.{$ownerId}.{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $default, $ownerId) {
            $setting = static::where('key', $key)->where('owner_id', $ownerId)->first();

            if (! $setting && $ownerId !== null) {
                $setting = static::where('key', $key)->whereNull('owner_id')->first();
            }

            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Get a setting value for an explicit owner (no Auth context needed).
     * Falls back to the global value, then to defaults(). Useful in console /
     * queued contexts (e.g. mailers) where there is no authenticated user.
     */
    public static function getForOwner(string $key, ?int $ownerId, mixed $default = null): mixed
    {
        $default ??= static::defaults()[$key] ?? null;

        $setting = static::withoutGlobalScopes()->where('key', $key)->where('owner_id', $ownerId)->first();

        if (! $setting && $ownerId !== null) {
            $setting = static::withoutGlobalScopes()->where('key', $key)->whereNull('owner_id')->first();
        }

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value (scoped to the current owner, or global for branding).
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        $ownerId = static::ownerScope($key);

        static::updateOrCreate(
            ['key' => $key, 'owner_id' => $ownerId],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget("setting.{$ownerId}.{$key}");
    }

    /**
     * Get all settings in a group as key-value array (owner-scoped over global).
     */
    public static function getGroup(string $group): array
    {
        $ownerId = Auth::check() ? Auth::user()->ownerId() : null;

        $global = static::where('group', $group)->whereNull('owner_id')->pluck('value', 'key')->toArray();
        $scoped = $ownerId === null
            ? []
            : static::where('group', $group)->where('owner_id', $ownerId)->pluck('value', 'key')->toArray();

        return array_merge($global, $scoped);
    }

    /**
     * Default values for all settings.
     */
    public static function defaults(): array
    {
        return [
            // General
            'app_name' => 'Living Kost',
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
