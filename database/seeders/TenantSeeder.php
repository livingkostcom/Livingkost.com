<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantData = [
            [
                'name' => 'Ahmad Hidayat',
                'email' => 'ahmad@example.com',
                'nik' => '3201101234567890',
                'phone' => '081234567890',
                'emergency_contact' => 'Ibu Siti (081111111111)',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@example.com',
                'nik' => '3201102234567890',
                'phone' => '082345678901',
                'emergency_contact' => 'Ibu Ratna (082222222222)',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'nik' => '3201103234567890',
                'phone' => '083456789012',
                'emergency_contact' => 'Pak Hendra (083333333333)',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@example.com',
                'nik' => '3201104234567890',
                'phone' => '084567890123',
                'emergency_contact' => 'Ibu Nani (084444444444)',
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko@example.com',
                'nik' => '3201105234567890',
                'phone' => '085678901234',
                'emergency_contact' => 'Pak Bambang (085555555555)',
            ],
        ];

        foreach ($tenantData as $data) {
            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password123'),
            ]);
            $user->assignRole('tenant');

            // Create tenant profile
            Tenant::create([
                'user_id' => $user->id,
                'nik' => $data['nik'],
                'phone' => $data['phone'],
                'emergency_contact' => $data['emergency_contact'],
                'status' => 'active',
            ]);
        }
    }
}
