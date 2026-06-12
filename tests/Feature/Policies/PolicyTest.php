<?php

use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->owner = User::factory()->create();
    $this->owner->assignRole('owner');

    $this->manager = User::factory()->create();
    $this->manager->assignRole('manager');

    $this->tenantUser = User::factory()->create();
    $this->tenantUser->assignRole('tenant');
    $this->tenant = Tenant::factory()->create(['user_id' => $this->tenantUser->id]);

    $this->room = Room::factory()->create();
    $this->lease = Lease::factory()->create([
        'tenant_id' => $this->tenant->id,
        'room_id' => $this->room->id,
    ]);
    $this->invoice = Invoice::factory()->create(['lease_id' => $this->lease->id]);
});

// --- Invoice Policy ---
test('owner can view any invoices', function () {
    expect($this->owner->can('viewAny', Invoice::class))->toBeTrue();
});

test('tenant can view any invoices (has view-invoices permission)', function () {
    expect($this->tenantUser->can('viewAny', Invoice::class))->toBeTrue();
});

test('tenant can view their own invoice', function () {
    expect($this->tenantUser->can('view', $this->invoice))->toBeTrue();
});

test('tenant cannot view other tenant invoice', function () {
    $otherTenant = Tenant::factory()->create();
    $otherLease = Lease::factory()->create(['tenant_id' => $otherTenant->id]);
    $otherInvoice = Invoice::factory()->create(['lease_id' => $otherLease->id]);

    expect($this->tenantUser->can('view', $otherInvoice))->toBeFalse();
});

test('owner can create invoice', function () {
    expect($this->owner->can('create', Invoice::class))->toBeTrue();
});

test('tenant cannot create invoice', function () {
    expect($this->tenantUser->can('create', Invoice::class))->toBeFalse();
});

test('owner can update unverified invoice', function () {
    expect($this->owner->can('update', $this->invoice))->toBeTrue();
});

test('owner cannot update verified invoice', function () {
    $this->invoice->update(['verified_at' => now()]);

    expect($this->owner->can('update', $this->invoice))->toBeFalse();
});

test('owner can delete unverified invoice', function () {
    expect($this->owner->can('delete', $this->invoice))->toBeTrue();
});

test('owner cannot delete verified invoice', function () {
    $this->invoice->update(['verified_at' => now()]);

    expect($this->owner->can('delete', $this->invoice))->toBeFalse();
});

// --- Lease Policy ---
test('owner can view any leases', function () {
    expect($this->owner->can('viewAny', Lease::class))->toBeTrue();
});

test('tenant can view any leases', function () {
    expect($this->tenantUser->can('viewAny', Lease::class))->toBeTrue();
});

test('tenant can view their own lease', function () {
    expect($this->tenantUser->can('view', $this->lease))->toBeTrue();
});

test('tenant cannot view other tenant lease', function () {
    $otherTenant = Tenant::factory()->create();
    $otherLease = Lease::factory()->create(['tenant_id' => $otherTenant->id]);

    expect($this->tenantUser->can('view', $otherLease))->toBeFalse();
});

test('owner can create lease', function () {
    expect($this->owner->can('create', Lease::class))->toBeTrue();
});

test('tenant cannot create lease', function () {
    expect($this->tenantUser->can('create', Lease::class))->toBeFalse();
});

test('cannot update completed lease', function () {
    $this->lease->update(['status' => 'completed']);

    expect($this->owner->can('update', $this->lease))->toBeFalse();
});

test('can only delete pending or cancelled lease', function () {
    $this->lease->update(['status' => 'active']);
    expect($this->owner->can('delete', $this->lease))->toBeFalse();

    $this->lease->update(['status' => 'pending']);
    expect($this->owner->can('delete', $this->lease))->toBeTrue();
});

// --- Room Policy ---
test('owner can view rooms', function () {
    expect($this->owner->can('viewAny', \App\Models\Room::class))->toBeTrue();
});

test('tenant cannot view rooms', function () {
    expect($this->tenantUser->can('viewAny', \App\Models\Room::class))->toBeFalse();
});

test('cannot update occupied room', function () {
    $this->room->update(['status' => 'occupied']);

    expect($this->owner->can('update', $this->room))->toBeFalse();
});

test('cannot delete occupied room', function () {
    $this->room->update(['status' => 'occupied']);

    expect($this->owner->can('delete', $this->room))->toBeFalse();
});
