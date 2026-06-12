<?php

use App\Models\Property;
use App\Models\RoomType;
use App\Models\Room;

test('room type belongs to property', function () {
    $roomType = RoomType::factory()->create();

    expect($roomType->property)->toBeInstanceOf(Property::class);
});

test('room type has many rooms', function () {
    $roomType = RoomType::factory()->create();
    Room::factory()->count(4)->create(['room_type_id' => $roomType->id]);

    expect($roomType->rooms)->toHaveCount(4);
});

test('room type casts facilities as json', function () {
    $facilities = ['AC', 'WiFi', 'Kamar Mandi Dalam'];
    $roomType = RoomType::factory()->create(['facilities' => $facilities]);

    $roomType->refresh();

    expect($roomType->facilities)->toBe($facilities);
});

test('room type casts price as decimal', function () {
    $roomType = RoomType::factory()->create(['price' => 1500000]);

    expect($roomType->price)->toBe('1500000.00');
});
