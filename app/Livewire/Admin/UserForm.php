<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UserForm extends Component
{
    public ?int $userId = null;
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $role = '';

    public function mount(?int $userId = null)
    {
        $this->userId = $userId;

        if ($userId) {
            $user = User::findOrFail($userId);
            $this->authorize('update', $user);
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone ?? '';
            $this->role = $user->getRoleNames()->first() ?? '';
        } else {
            $this->authorize('create', User::class);
            $this->role = $this->roleChoices()[0] ?? '';
        }
    }

    /**
     * Roles the current actor may assign in this form.
     * - Owner: manager & tenant (their sub-users)
     * - Super-admin: owner (on create); any of owner/manager/tenant when editing
     */
    public function roleChoices(): array
    {
        $actor = Auth::user();

        if ($actor->isSuperAdmin()) {
            return $this->userId ? ['owner', 'manager', 'tenant'] : ['owner'];
        }

        return ['manager', 'tenant'];
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->userId)],
            'phone' => 'nullable|string|max:20',
            'password' => $this->userId ? 'nullable|string|min:6' : 'required|string|min:6',
            'role' => ['required', Rule::in($this->roleChoices())],
        ];
    }

    public function save()
    {
        $this->validate();

        $actor = Auth::user();

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $this->authorize('update', $user);

            $user->name = $this->name;
            $user->email = $this->email;
            $user->phone = $this->phone ?: null;
            if ($this->password !== '') {
                $user->password = Hash::make($this->password);
            }
            $user->owner_id = $this->resolveOwnerId($actor, $user);
            $user->save();
            $user->syncRoles([$this->role]);

            session()->flash('message', 'User berhasil diperbarui!');
        } else {
            $this->authorize('create', User::class);

            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone ?: null,
                'password' => Hash::make($this->password),
                'owner_id' => $this->resolveOwnerId($actor, null),
                'email_verified_at' => now(),
            ]);
            $user->assignRole($this->role);

            session()->flash('message', 'User berhasil dibuat!');
        }

        $this->dispatch('user-saved');
    }

    /**
     * Determine owner_id for the managed user.
     * - Owner actor: the new/edited user belongs to the owner.
     * - Super-admin: owner role => null; otherwise preserve existing owner.
     */
    private function resolveOwnerId(User $actor, ?User $existing): ?int
    {
        if (! $actor->isSuperAdmin()) {
            return $actor->id;
        }

        return $this->role === 'owner' ? null : ($existing?->owner_id);
    }

    public function render()
    {
        return view('livewire.admin.user-form');
    }
}
