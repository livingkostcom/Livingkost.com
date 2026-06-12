<?php

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

// --- Dashboard ---
test('owner can access dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('owner');

    $this->actingAs($user)->get('/dashboard')->assertOk();
});

test('tenant can access dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('tenant');
    Tenant::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->get('/dashboard')->assertOk();
});

// --- Properties ---
test('owner can access properties page', function () {
    $user = User::factory()->create();
    $user->assignRole('owner');

    $this->actingAs($user)->get('/properties')->assertOk();
});

test('tenant cannot access properties page', function () {
    $user = User::factory()->create();
    $user->assignRole('tenant');

    $this->actingAs($user)->get('/properties')->assertForbidden();
});

// --- Room Types ---
test('owner can access room types page', function () {
    $user = User::factory()->create();
    $user->assignRole('owner');

    $this->actingAs($user)->get('/room-types')->assertOk();
});

test('tenant cannot access room types page', function () {
    $user = User::factory()->create();
    $user->assignRole('tenant');

    $this->actingAs($user)->get('/room-types')->assertForbidden();
});

// --- Rooms ---
test('owner can access rooms page', function () {
    $user = User::factory()->create();
    $user->assignRole('owner');

    $this->actingAs($user)->get('/rooms')->assertOk();
});

test('tenant cannot access rooms page', function () {
    $user = User::factory()->create();
    $user->assignRole('tenant');

    $this->actingAs($user)->get('/rooms')->assertForbidden();
});

// --- Tenants ---
test('owner can access tenants page', function () {
    $user = User::factory()->create();
    $user->assignRole('owner');

    $this->actingAs($user)->get('/tenants')->assertOk();
});

test('tenant cannot access tenants page', function () {
    $user = User::factory()->create();
    $user->assignRole('tenant');

    $this->actingAs($user)->get('/tenants')->assertForbidden();
});

// --- Leases ---
test('owner can access leases page', function () {
    $user = User::factory()->create();
    $user->assignRole('owner');

    $this->actingAs($user)->get('/leases')->assertOk();
});

// --- Invoices ---
test('owner can access invoices page', function () {
    $user = User::factory()->create();
    $user->assignRole('owner');

    $this->actingAs($user)->get('/invoices')->assertOk();
});

test('tenant can access tenant invoices page', function () {
    $user = User::factory()->create();
    $user->assignRole('tenant');

    $this->actingAs($user)->get('/tenant/invoices')->assertOk();
});

// --- Maintenance ---
test('owner can access maintenance page', function () {
    $user = User::factory()->create();
    $user->assignRole('owner');

    $this->actingAs($user)->get('/maintenance')->assertOk();
});

test('tenant can access maintenance page', function () {
    $user = User::factory()->create();
    $user->assignRole('tenant');
    Tenant::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->get('/maintenance')->assertOk();
});

// --- Manager Access ---
test('manager can access properties page', function () {
    $user = User::factory()->create();
    $user->assignRole('manager');

    $this->actingAs($user)->get('/properties')->assertOk();
});

test('manager can access invoices page', function () {
    $user = User::factory()->create();
    $user->assignRole('manager');

    $this->actingAs($user)->get('/invoices')->assertOk();
});

// --- Guest Access ---
test('guest cannot access any protected routes', function () {
    $protectedRoutes = [
        '/dashboard',
        '/properties',
        '/room-types',
        '/rooms',
        '/tenants',
        '/leases',
        '/invoices',
        '/maintenance',
        '/expenses',
    ];

    foreach ($protectedRoutes as $route) {
        $this->get($route)->assertRedirect('/login');
    }
});
