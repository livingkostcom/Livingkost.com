<?php

use App\Models\Lease;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Tenant;

test('room belongs to room type', function () {
    $room = Room::factory()->create();

    expect($room->roomType)->toBeInstanceOf(RoomType::class);
});

test('room has many leases', function () {
    $room = Room::factory()->create();
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();
    Lease::factory()->create(['room_id' => $room->id, 'tenant_id' => $tenant1->id]);
    Lease::factory()->completed()->create(['room_id' => $room->id, 'tenant_id' => $tenant2->id]);

    expect($room->leases)->toHaveCount(2);
});

test('room active lease returns the active lease', function () {
    $room = Room::factory()->create();
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();

    Lease::factory()->completed()->create(['room_id' => $room->id, 'tenant_id' => $tenant1->id]);
    $activeLease = Lease::factory()->create(['room_id' => $room->id, 'tenant_id' => $tenant2->id, 'status' => 'active']);

    expect($room->activeLease())->not->toBeNull()
        ->and($room->activeLease()->id)->toBe($activeLease->id);
});

test('room active lease returns null when no active lease', function () {
    $room = Room::factory()->create();

    expect($room->activeLease())->toBeNull();
});

test('room property accessor returns property through room type', function () {
    $property = Property::factory()->create();
    $roomType = RoomType::factory()->create(['property_id' => $property->id]);
    $room = Room::factory()->create(['room_type_id' => $roomType->id]);

    expect($room->property->id)->toBe($property->id);
});

test('room factory states work correctly', function () {
    expect(Room::factory()->occupied()->create()->status)->toBe('occupied')
        ->and(Room::factory()->maintenance()->create()->status)->toBe('maintenance');
});
