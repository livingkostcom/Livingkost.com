<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions first
        $this->call(RoleAndPermissionSeeder::class);

        // Create test users with admin roles
        $owner = User::factory()->create([
            'name' => 'Owner',
            'email' => 'owner@livingkost.com',
        ]);
        $owner->assignRole('owner');

        $manager = User::factory()->create([
            'name' => 'Manager',
            'email' => 'manager@livingkost.com',
        ]);
        $manager->assignRole('manager');

        // Seed properties and room types
        $this->call(PropertySeeder::class);

        // Seed rooms
        $this->call(RoomSeeder::class);

        // Seed tenants
        $this->call(TenantSeeder::class);

        // Seed leases
        $this->call(LeaseSeeder::class);

        // Seed invoices
        $this->call(InvoiceSeeder::class);

        // Seed expenses
        $this->call(ExpenseSeeder::class);

        // Seed announcements
        $this->call(AnnouncementSeeder::class);

        // Seed settings
        $this->call(SettingSeeder::class);

        // Clear permission cache after all seeders
        app()['cache']->forget('spatie.permission.cache');
    }
}

