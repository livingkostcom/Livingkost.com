<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // General
            ['key' => 'app_name', 'value' => 'Living Kost', 'group' => 'general'],
            ['key' => 'app_tagline', 'value' => 'Sistem Manajemen Kos', 'group' => 'general'],
            ['key' => 'app_address', 'value' => '', 'group' => 'general'],
            ['key' => 'app_phone', 'value' => '', 'group' => 'general'],
            ['key' => 'app_email', 'value' => '', 'group' => 'general'],

            // Payment
            ['key' => 'bank_name', 'value' => '', 'group' => 'payment'],
            ['key' => 'bank_account_number', 'value' => '', 'group' => 'payment'],
            ['key' => 'bank_account_holder', 'value' => '', 'group' => 'payment'],
            ['key' => 'payment_instructions', 'value' => '', 'group' => 'payment'],

            // Late Fee
            ['key' => 'late_fee_enabled', 'value' => '0', 'group' => 'late_fee'],
            ['key' => 'late_fee_type', 'value' => 'fixed', 'group' => 'late_fee'],
            ['key' => 'late_fee_amount', 'value' => '0', 'group' => 'late_fee'],
            ['key' => 'late_fee_grace_days', 'value' => '3', 'group' => 'late_fee'],
        ];

        foreach ($defaults as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
