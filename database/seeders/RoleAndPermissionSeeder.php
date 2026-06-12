<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Define all permissions
        $permissions = [
            // Property Management
            'view-properties',
            'create-property',
            'edit-property',
            'delete-property',

            // Room Type Management
            'view-room-types',
            'create-room-type',
            'edit-room-type',
            'delete-room-type',

            // Room Management
            'view-rooms',
            'create-room',
            'edit-room',
            'delete-room',
            'check-in-tenant',
            'check-out-tenant',

            // Tenant Management
            'view-tenants',
            'create-tenant',
            'edit-tenant',
            'delete-tenant',

            // Lease Management
            'view-leases',
            'create-lease',
            'edit-lease',
            'delete-lease',

            // Invoice/Payment
            'view-invoices',
            'create-invoice',
            'verify-payment',
            'upload-payment-proof',

            // Reports
            'view-reports',
            'view-income-report',

            // Maintenance
            'view-maintenance',
            'create-maintenance',
            'manage-maintenance',

            // Expenses
            'view-expenses',
            'manage-expenses',

            // Announcements
            'view-announcements',
            'manage-announcements',

            // Settings
            'manage-settings',

            // User Management
            'manage-users',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Owner role - Full access
        $ownerRole = Role::firstOrCreate(['name' => 'owner']);
        $ownerRole->syncPermissions($permissions);

        // Manager role - Operational tasks
        $managerPermissions = [
            'view-properties',
            'view-room-types',
            'create-room-type',
            'edit-room-type',
            'view-rooms',
            'create-room',
            'edit-room',
            'check-in-tenant',
            'check-out-tenant',
            'view-tenants',
            'create-tenant',
            'edit-tenant',
            'view-leases',
            'create-lease',
            'edit-lease',
            'view-invoices',
            'verify-payment',
            'view-reports',
            'view-income-report',
            'view-maintenance',
            'manage-maintenance',
            'view-expenses',
            'manage-expenses',
            'view-announcements',
            'manage-announcements',
        ];
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->syncPermissions($managerPermissions);

        // Tenant role - Self-service
        $tenantPermissions = [
            'view-invoices',
            'upload-payment-proof',
            'view-maintenance',
            'create-maintenance',
            'view-announcements',
        ];
        $tenantRole = Role::firstOrCreate(['name' => 'tenant']);
        $tenantRole->syncPermissions($tenantPermissions);
    }
}
