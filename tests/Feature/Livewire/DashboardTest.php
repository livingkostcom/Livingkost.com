<?php

use App\Livewire\Dashboard;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('dashboard renders for owner', function () {
    $owner = User::factory()->create();
    $owner->assignRole('owner');

    Livewire::actingAs($owner)
        ->test(Dashboard::class)
        ->assertOk()
        ->assertViewHas('isOwner', true)
        ->assertViewHas('isTenant', false);
});

test('dashboard renders for tenant', function () {
    $user = User::factory()->create();
    $user->assignRole('tenant');
    Tenant::factory()->create(['user_id' => $user->id]);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertOk()
        ->assertViewHas('isTenant', true)
        ->assertViewHas('isOwner', false);
});

test('dashboard owner metrics include correct data', function () {
    $owner = User::factory()->create();
    $owner->assignRole('owner');

    // Create test data
    $property = Property::factory()->create();
    $roomType = RoomType::factory()->create(['property_id' => $property->id]);
    $room = Room::factory()->create(['room_type_id' => $roomType->id, 'status' => 'occupied']);
    $tenant = Tenant::factory()->create();
    $lease = Lease::factory()->create(['tenant_id' => $tenant->id, 'room_id' => $room->id, 'status' => 'active']);

    $component = Livewire::actingAs($owner)->test(Dashboard::class);
    $metrics = $component->viewData('metrics');

    expect($metrics)->toBeArray()
        ->and($metrics)->toHaveKey('total_rooms')
        ->and($metrics)->toHaveKey('occupied_rooms')
        ->and($metrics)->toHaveKey('active_tenants')
        ->and($metrics)->toHaveKey('pending_payments')
        ->and($metrics)->toHaveKey('income_this_month')
        ->and($metrics)->toHaveKey('pending_maintenance');
});

test('dashboard tenant data returns correct structure', function () {
    $user = User::factory()->create();
    $user->assignRole('tenant');
    $tenant = Tenant::factory()->create(['user_id' => $user->id]);

    $component = Livewire::actingAs($user)->test(Dashboard::class);
    $tenantData = $component->viewData('tenant');

    expect($tenantData)->toBeArray()
        ->and($tenantData)->toHaveKeys(['lease', 'unpaid', 'next_invoice', 'maintenance']);
});
