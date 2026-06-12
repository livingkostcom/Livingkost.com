<?php

use App\Models\Announcement;
use App\Models\Property;
use App\Models\User;

test('announcement can be created with factory', function () {
    $announcement = Announcement::factory()->create();

    expect($announcement)->toBeInstanceOf(Announcement::class)
        ->and($announcement->is_active)->toBeTrue();
});

test('announcement belongs to property', function () {
    $property = Property::factory()->create();
    $announcement = Announcement::factory()->create(['property_id' => $property->id]);

    expect($announcement->property)->toBeInstanceOf(Property::class)
        ->and($announcement->property->id)->toBe($property->id);
});

test('announcement belongs to creator', function () {
    $announcement = Announcement::factory()->create();

    expect($announcement->creator)->toBeInstanceOf(User::class);
});

test('announcement scope active returns only active announcements', function () {
    Announcement::factory()->create(); // active, published now
    Announcement::factory()->expired()->create(); // expired
    Announcement::factory()->inactive()->create(); // inactive

    expect(Announcement::active()->count())->toBe(1);
});

test('announcement scope active includes announcements without expiry', function () {
    Announcement::factory()->create(['expires_at' => null]);

    expect(Announcement::active()->count())->toBe(1);
});

test('announcement scope active excludes future published announcements', function () {
    Announcement::factory()->create(['published_at' => now()->addDays(5)]);

    expect(Announcement::active()->count())->toBe(0);
});

test('announcement is read by user', function () {
    $user = User::factory()->create();
    $announcement = Announcement::factory()->create();

    expect($announcement->isReadBy($user))->toBeFalse();

    $announcement->readByUsers()->attach($user->id, ['read_at' => now()]);

    expect($announcement->isReadBy($user))->toBeTrue();
});

test('announcement get priority label returns correct labels', function () {
    expect(Announcement::getPriorityLabel('urgent'))->toBe('Darurat')
        ->and(Announcement::getPriorityLabel('important'))->toBe('Penting')
        ->and(Announcement::getPriorityLabel('normal'))->toBe('Normal');
});

test('announcement uses soft deletes', function () {
    $announcement = Announcement::factory()->create();
    $announcement->delete();

    expect(Announcement::count())->toBe(0)
        ->and(Announcement::withTrashed()->count())->toBe(1);
});

test('announcement casts dates properly', function () {
    $announcement = Announcement::factory()->create([
        'published_at' => '2025-03-01',
        'expires_at' => '2025-04-01',
    ]);

    expect($announcement->published_at)->toBeInstanceOf(Carbon\Carbon::class)
        ->and($announcement->expires_at)->toBeInstanceOf(Carbon\Carbon::class);
});

test('announcement factory states work correctly', function () {
    $expired = Announcement::factory()->expired()->create();
    $inactive = Announcement::factory()->inactive()->create();
    $urgent = Announcement::factory()->urgent()->create();

    expect($expired->expires_at->isPast())->toBeTrue()
        ->and($inactive->is_active)->toBeFalse()
        ->and($urgent->priority)->toBe('urgent');
});
