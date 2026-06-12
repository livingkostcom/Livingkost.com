<?php

use App\Livewire\Admin\AnnouncementIndex;
use App\Models\Announcement;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    $this->owner = User::factory()->create();
    $this->owner->assignRole('owner');
});

test('announcement index component renders for owner', function () {
    Livewire::actingAs($this->owner)
        ->test(AnnouncementIndex::class)
        ->assertOk();
});

test('announcement index shows announcements', function () {
    $announcement = Announcement::factory()->create(['created_by' => $this->owner->id]);

    Livewire::actingAs($this->owner)
        ->test(AnnouncementIndex::class)
        ->assertSee($announcement->title);
});

test('announcement index can create announcement', function () {
    Livewire::actingAs($this->owner)
        ->test(AnnouncementIndex::class)
        ->call('openCreateModal')
        ->assertSet('showModal', true)
        ->set('title', 'Test Announcement')
        ->set('content', 'Test content here')
        ->set('priority', 'normal')
        ->set('target', 'all')
        ->set('published_at', now()->format('Y-m-d'))
        ->call('save')
        ->assertHasNoErrors();

    expect(Announcement::where('title', 'Test Announcement')->exists())->toBeTrue();
});

test('announcement index validates required fields', function () {
    Livewire::actingAs($this->owner)
        ->test(AnnouncementIndex::class)
        ->call('openCreateModal')
        ->set('title', '')
        ->set('content', '')
        ->call('save')
        ->assertHasErrors(['title', 'content']);
});

test('announcement index can toggle active status', function () {
    $announcement = Announcement::factory()->create(['created_by' => $this->owner->id, 'is_active' => true]);

    Livewire::actingAs($this->owner)
        ->test(AnnouncementIndex::class)
        ->call('toggleActive', $announcement->id);

    expect($announcement->fresh()->is_active)->toBeFalse();
});

test('announcement index can delete announcement', function () {
    $announcement = Announcement::factory()->create(['created_by' => $this->owner->id]);

    Livewire::actingAs($this->owner)
        ->test(AnnouncementIndex::class)
        ->call('confirmDelete', $announcement->id)
        ->call('delete');

    expect(Announcement::find($announcement->id))->toBeNull();
});

test('announcement index can open detail modal', function () {
    $announcement = Announcement::factory()->create(['created_by' => $this->owner->id]);

    Livewire::actingAs($this->owner)
        ->test(AnnouncementIndex::class)
        ->call('openDetailModal', $announcement->id)
        ->assertSet('showDetailModal', true);
});

test('announcement index can edit announcement', function () {
    $announcement = Announcement::factory()->create(['created_by' => $this->owner->id]);

    Livewire::actingAs($this->owner)
        ->test(AnnouncementIndex::class)
        ->call('openEditModal', $announcement->id)
        ->assertSet('isEditing', true)
        ->assertSet('title', $announcement->title)
        ->set('title', 'Updated Title')
        ->call('save')
        ->assertHasNoErrors();

    expect($announcement->fresh()->title)->toBe('Updated Title');
});

test('announcement index denies access to tenant', function () {
    $tenant = User::factory()->create();
    $tenant->assignRole('tenant');

    Livewire::actingAs($tenant)
        ->test(AnnouncementIndex::class)
        ->assertForbidden();
});
