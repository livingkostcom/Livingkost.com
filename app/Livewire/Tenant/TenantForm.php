<?php

namespace App\Livewire\Tenant;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithFileUploads;

class TenantForm extends Component
{
    use WithFileUploads;

    public ?int $tenantId = null;

    #[\Livewire\Attributes\Validate('required|string|max:100')]
    public string $name = '';

    #[\Livewire\Attributes\Validate('required|string|email|max:100')]
    public string $email = '';

    #[\Livewire\Attributes\Validate('required|string|size:16|unique:tenants,nik')]
    public string $nik = '';

    #[\Livewire\Attributes\Validate('required|string|max:20')]
    public string $phone = '';

    #[\Livewire\Attributes\Validate('nullable|string|max:100')]
    public string $emergency_contact = '';

    #[\Livewire\Attributes\Validate('nullable|image|max:2048')]
    public $avatar = null;

    #[\Livewire\Attributes\Validate('nullable|image|max:2048')]
    public $ktp_photo = null;

    #[\Livewire\Attributes\Validate('required|string|in:active,inactive,evicted')]
    public string $status = 'active';

    public function mount(?int $tenantId = null)
    {
        $this->tenantId = $tenantId;

        if ($tenantId) {
            $tenant = Tenant::findOrFail($tenantId);
            $this->authorize('update', $tenant);
            
            $this->name = $tenant->name;
            $this->email = $tenant->email ?? '';
            $this->nik = $tenant->nik;
            $this->phone = $tenant->phone;
            $this->emergency_contact = $tenant->emergency_contact ?? '';
            $this->status = $tenant->status;
        }
    }

    public function save()
    {
        // If editing, remove unique constraint for same nik and email
        $this->validate(
            $this->tenantId ? [
                'name' => 'required|string|max:100',
                'email' => 'required|string|email|max:100|unique:tenants,email,' . $this->tenantId,
                'nik' => 'required|string|size:16|unique:tenants,nik,' . $this->tenantId,
                'phone' => 'required|string|max:20',
                'emergency_contact' => 'nullable|string|max:100',
                'avatar' => 'nullable|image|max:2048',
                'ktp_photo' => 'nullable|image|max:2048',
                'status' => 'required|string|in:active,inactive,evicted',
            ] : $this->getValidationRules()
        );

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'nik' => $this->nik,
            'phone' => $this->phone,
            'emergency_contact' => $this->emergency_contact ?: null,
            'status' => $this->status,
        ];

        if ($this->avatar && $this->avatar instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $data['avatar'] = $this->avatar->store('avatars', 'public');
        }

        if ($this->ktp_photo && $this->ktp_photo instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            $data['ktp_photo'] = $this->ktp_photo->store('ktp-photos', 'public');
        }

        if ($this->tenantId) {
            $tenant = Tenant::findOrFail($this->tenantId);
            $this->authorize('update', $tenant);
            $data['updated_by'] = auth()->user()->name;
            $tenant->update($data);
            $message = 'Penyewa berhasil diperbarui!';
        } else {
            $this->authorize('create', Tenant::class);
            $data['created_by'] = auth()->user()->name;
            Tenant::create($data);
            $message = 'Penyewa berhasil ditambahkan!';
        }

        $this->dispatch('tenant-saved', message: $message);
    }

    private function getValidationRules()
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:tenants,email',
            'nik' => 'required|string|size:16|unique:tenants,nik',
            'phone' => 'required|string|max:20',
            'emergency_contact' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|max:2048',
            'ktp_photo' => 'nullable|image|max:2048',
            'status' => 'required|string|in:active,inactive,evicted',
        ];
    }

    public function render()
    {
        return view('livewire.tenant.tenant-form');
    }
}
