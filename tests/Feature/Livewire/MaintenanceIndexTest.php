<?php

use App\Livewire\Admin\MaintenanceIndex;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->owner = User::factory()->create();
    $this->owner->assignRole('owner');
});

test('maintenance index component renders for owner', function () {
    Livewire::actingAs($this->owner)
        ->test(MaintenanceIndex::class)
        ->assertOk();
});

test('maintenance index shows requests', function () {
    $tenant = Tenant::factory()->create();
    $room = Room::factory()->create();
    $request = MaintenanceRequest::factory()->create([
        'tenant_id' => $tenant->id,
        'room_id' => $room->id,
    ]);

    Livewire::actingAs($this->owner)
        ->test(MaintenanceIndex::class)
        ->assertSee($request->title);
});

test('maintenance index can filter by status', function () {
    $tenant = Tenant::factory()->create();
    $room = Room::factory()->create();

    MaintenanceRequest::factory()->create([
        'tenant_id' => $tenant->id,
        'room_id' => $room->id,
        'title' => 'Pending Request',
        'status' => 'pending',
    ]);
    MaintenanceRequest::factory()->completed()->create([
        'tenant_id' => $tenant->id,
        'room_id' => $room->id,
        'title' => 'Completed Request',
        'resolved_by' => $this->owner->id,
    ]);

    Livewire::actingAs($this->owner)
        ->test(MaintenanceIndex::class)
        ->set('statusFilter', 'pending')
        ->assertSee('Pending Request')
        ->assertDontSee('Completed Request');
});
