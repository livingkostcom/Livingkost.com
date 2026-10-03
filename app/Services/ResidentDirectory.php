<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ResidentDirectory
{
    public static function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    // Access settings deliberately have no fallback to another owner's settings.
    public static function phones(int $ownerId): array
    {
        $value = Setting::where('owner_id', $ownerId)->where('key', 'resident_directory_phones')->value('value');
        $phones = json_decode($value ?? '[]', true);

        return is_array($phones) ? $phones : [];
    }

    public static function residents(int $ownerId): Builder
    {
        return DB::table('leases as l')
            ->join('tenants as t', 't.id', '=', 'l.tenant_id')
            ->join('rooms as r', 'r.id', '=', 'l.room_id')
            ->join('room_types as rt', 'rt.id', '=', 'r.room_type_id')
            ->join('properties as p', 'p.id', '=', 'rt.property_id')
            ->where('l.owner_id', $ownerId)
            ->where('t.owner_id', $ownerId)
            ->where('r.owner_id', $ownerId)
            ->where('rt.owner_id', $ownerId)
            ->where('p.owner_id', $ownerId)
            ->where('l.status', 'active')->whereNull('l.deleted_at')
            ->where('t.status', 'active')->whereNull('t.deleted_at')
            ->whereDate('l.start_date', '<=', today())
            ->select('l.id as lease_id', 'l.start_date', 't.id as tenant_id', 't.name', 't.phone', 't.nik', 't.ktp_photo', 'r.room_number', 'p.name as property_name')
            ->orderBy('p.name')->orderBy('r.room_number')->orderBy('t.name');
    }
}
