<?php

namespace App\Livewire\Tenant;

use App\Mail\TenantWelcomeMail;
use App\Models\Tenant;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

    #[\Livewire\Attributes\Validate('nullable|mimes:jpeg,jpg,png,gif,webp,avif|max:2048')]
    public $avatar = null;

    #[\Livewire\Attributes\Validate('nullable|mimes:jpeg,jpg,png,gif,webp,avif|max:2048')]
    public $ktp_photo = null;

    #[\Livewire\Attributes\Validate('required|string|in:active,inactive,evicted')]
    public string $status = 'active';

    public function mount(?int $tenantId = null)
    {
        $this->tenantId = $tenantId;

        if ($tenantId) {
            $tenant = Tenant::findOrFail($tenantId);
            $this->authorize('update', $tenant);
            
            // Coalesce nullable columns — the typed string properties cannot
            // accept null (would throw TypeError when opening the edit form).
            $this->name = $tenant->name ?? '';
            $this->email = $tenant->email ?? '';
            $this->nik = $tenant->nik ?? '';
            $this->phone = $tenant->phone ?? '';
            $this->emergency_contact = $tenant->emergency_contact ?? '';
            $this->status = $tenant->status ?? 'active';
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
                'avatar' => 'nullable|mimes:jpeg,jpg,png,gif,webp,avif|max:2048',
                'ktp_photo' => 'nullable|mimes:jpeg,jpg,png,gif,webp,avif|max:2048',
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

            // Keep the linked login account's name & phone in sync
            if ($tenant->user_id && $tenant->user) {
                $tenant->user->update(['name' => $this->name, 'phone' => $this->phone]);
            }

            $message = 'Penyewa berhasil diperbarui!';
        } else {
            $this->authorize('create', Tenant::class);
            $data['created_by'] = auth()->user()->name;

            // Create (or link) a login account for this tenant
            $ownerId = auth()->user()->ownerId();
            $plainPassword = null;
            $user = User::where('email', $this->email)->first();

            if (! $user) {
                $plainPassword = Str::password(10, true, true, false);
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'password' => Hash::make($plainPassword),
                    'owner_id' => $ownerId,
                    'email_verified_at' => now(),
                ]);
            }

            if (! $user->hasRole('tenant')) {
                $user->assignRole('tenant');
            }
            if (is_null($user->owner_id)) {
                $user->owner_id = $ownerId;
                $user->save();
            }

            $data['user_id'] = $user->id;
            Tenant::create($data);

            // First-time account: send login credentials via email + WhatsApp
            if ($plainPassword) {
                $this->sendCredentials($this->name, $this->email, $this->phone, $plainPassword);

                $message = "Penyewa & akun login dibuat — kredensial dikirim ke email & WhatsApp penyewa. (Email: {$this->email} · Password: {$plainPassword})";
            } else {
                $message = 'Penyewa ditambahkan & ditautkan ke akun login yang sudah ada.';
            }
        }

        $this->dispatch('tenant-saved', message: $message);
    }

    /**
     * Send the new login credentials to the tenant via email and WhatsApp.
     * Failures are logged but never block tenant creation.
     */
    private function sendCredentials(string $name, string $email, string $phone, string $plainPassword): void
    {
        $loginUrl = route('login');

        // Email
        try {
            if (config('mail.default') !== 'log' && $email) {
                Mail::to($email)->send(new TenantWelcomeMail($name, $email, $plainPassword, $loginUrl));
            }
        } catch (\Throwable $e) {
            Log::error('Tenant welcome email failed', ['email' => $email, 'error' => $e->getMessage()]);
        }

        // WhatsApp
        try {
            if ($phone) {
                $waMessage = "Halo {$name},\n\n"
                    . "Selamat datang di *Living Kost*! Akun login Anda telah dibuat.\n\n"
                    . "Berikut detail akun untuk masuk ke portal penghuni:\n"
                    . "Email: {$email}\n"
                    . "Password: {$plainPassword}\n\n"
                    . "Login di sini: {$loginUrl}\n\n"
                    . "Demi keamanan, segera ganti password Anda setelah login pertama. Jangan bagikan password ini kepada siapa pun.\n\n"
                    . "_Living Kost_";
                WhatsAppService::send($phone, $waMessage);
            }
        } catch (\Throwable $e) {
            Log::error('Tenant welcome WhatsApp failed', ['phone' => $phone, 'error' => $e->getMessage()]);
        }
    }

    private function getValidationRules()
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:tenants,email',
            'nik' => 'required|string|size:16|unique:tenants,nik',
            'phone' => 'required|string|max:20',
            'emergency_contact' => 'nullable|string|max:100',
            'avatar' => 'nullable|mimes:jpeg,jpg,png,gif,webp,avif|max:2048',
            'ktp_photo' => 'nullable|mimes:jpeg,jpg,png,gif,webp,avif|max:2048',
            'status' => 'required|string|in:active,inactive,evicted',
        ];
    }

    public function render()
    {
        return view('livewire.tenant.tenant-form');
    }
}
