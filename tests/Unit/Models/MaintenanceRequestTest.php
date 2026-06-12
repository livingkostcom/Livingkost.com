<?php

use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;

test('maintenance request can be created with factory', function () {
    $request = MaintenanceRequest::factory()->create();

    expect($request)->toBeInstanceOf(MaintenanceRequest::class)
        ->and($request->status)->toBe('pending');
});

test('maintenance request belongs to tenant', function () {
    $request = MaintenanceRequest::factory()->create();

    expect($request->tenant)->toBeInstanceOf(Tenant::class);
});

test('maintenance request belongs to room', function () {
    $request = MaintenanceRequest::factory()->create();

    expect($request->room)->toBeInstanceOf(Room::class);
});

test('maintenance request has resolver relationship', function () {
    $user = User::factory()->create();
    $request = MaintenanceRequest::factory()->completed()->create(['resolved_by' => $user->id]);

    expect($request->resolver)->toBeInstanceOf(User::class)
        ->and($request->resolver->id)->toBe($user->id);
});

test('maintenance request casts resolved_at as datetime', function () {
    $request = MaintenanceRequest::factory()->completed()->create();

    expect($request->resolved_at)->toBeInstanceOf(Carbon\Carbon::class);
});

test('maintenance request factory states work', function () {
    $inProgress = MaintenanceRequest::factory()->inProgress()->create();
    $completed = MaintenanceRequest::factory()->completed()->create();

    expect($inProgress->status)->toBe('in_progress')
        ->and($completed->status)->toBe('completed')
        ->and($completed->resolved_at)->not->toBeNull();
});
