<?php

use App\Models\Lease;
use App\Models\Tenant;
use App\Models\User;

test('tenant can be created with factory', function () {
    $tenant = Tenant::factory()->create();

    expect($tenant)->toBeInstanceOf(Tenant::class)
        ->and($tenant->name)->not->toBeEmpty()
        ->and($tenant->email)->not->toBeEmpty()
        ->and($tenant->status)->toBe('active');
});

test('tenant belongs to user when user_id is set', function () {
    $user = User::factory()->create();
    $tenant = Tenant::factory()->withUser($user)->create();

    expect($tenant->user)->toBeInstanceOf(User::class)
        ->and($tenant->user->id)->toBe($user->id);
});

test('tenant has leases relationship', function () {
    $tenant = Tenant::factory()->create();
    $room = \App\Models\Room::factory()->create();
    Lease::factory()->create(['tenant_id' => $tenant->id, 'room_id' => $room->id]);

    expect($tenant->leases)->toHaveCount(1)
        ->and($tenant->leases->first())->toBeInstanceOf(Lease::class);
});

test('tenant display name returns user name when connected', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $tenant = Tenant::factory()->withUser($user)->create(['name' => 'Tenant Name']);

    expect($tenant->display_name)->toBe('John Doe');
});

test('tenant display name returns tenant name when no user', function () {
    $tenant = Tenant::factory()->create(['name' => 'Tenant Name', 'user_id' => null]);

    expect($tenant->display_name)->toBe('Tenant Name');
});

test('tenant uses soft deletes', function () {
    $tenant = Tenant::factory()->create();
    $tenant->delete();

    expect(Tenant::count())->toBe(0)
        ->and(Tenant::withTrashed()->count())->toBe(1);
});
