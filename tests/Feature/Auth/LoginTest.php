<?php

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('login page is accessible to guests', function () {
    $this->get('/login')->assertOk();
});

test('authenticated user is redirected from login page', function () {
    $user = User::factory()->create();
    $user->assignRole('owner');

    $this->actingAs($user)
        ->get('/login')
        ->assertRedirect(route('dashboard'));
});

test('unauthenticated user is redirected to login', function () {
    $this->get('/dashboard')
        ->assertRedirect('/login');
});

test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);
    $user->assignRole('owner');

    \Livewire\Livewire::test(\App\Livewire\Auth\Login::class)
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticated();
});

test('user cannot login with invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    \Livewire\Livewire::test(\App\Livewire\Auth\Login::class)
        ->set('email', 'test@example.com')
        ->set('password', 'wrongpassword')
        ->call('login')
        ->assertHasErrors('email');
});

test('login validates required fields', function () {
    \Livewire\Livewire::test(\App\Livewire\Auth\Login::class)
        ->set('email', '')
        ->set('password', '')
        ->call('login')
        ->assertHasErrors(['email', 'password']);
});

test('login validates email format', function () {
    \Livewire\Livewire::test(\App\Livewire\Auth\Login::class)
        ->set('email', 'not-an-email')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasErrors('email');
});
