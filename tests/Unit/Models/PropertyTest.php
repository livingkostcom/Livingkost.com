<?php

use App\Models\Property;
use App\Models\RoomType;
use App\Models\Room;

test('property can be created with factory', function () {
    $property = Property::factory()->create();

    expect($property)->toBeInstanceOf(Property::class)
        ->and($property->name)->not->toBeEmpty()
        ->and($property->address)->not->toBeEmpty()
        ->and($property->status)->toBe('active');
});

test('property has many room types', function () {
    $property = Property::factory()->create();
    RoomType::factory()->count(3)->create(['property_id' => $property->id]);

    expect($property->roomTypes)->toHaveCount(3);
});

test('property total rooms accessor works', function () {
    $property = Property::factory()->create();
    $roomType = RoomType::factory()->create(['property_id' => $property->id]);
    Room::factory()->count(5)->create(['room_type_id' => $roomType->id]);

    expect($property->total_rooms)->toBe(5);
});

test('property total rooms returns zero when no rooms', function () {
    $property = Property::factory()->create();

    expect($property->total_rooms)->toBe(0);
});
