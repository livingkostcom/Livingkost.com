<?php

namespace App\Livewire\Tenant;

use App\Mail\TenantWelcomeMail;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\TenantRegistration;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class RegistrationIndex extends Component
{
    use WithPagination;

    public string $filterStatus = 'pending';
    public bool $showShare = false;
    public string $notice = '';

    public function mount(): void
    {
        abort_unless(Auth::user()->can('view-tenants'), 403);
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    /** The public registration link for this owner (null for super-admin). */
    public function getShareUrlProperty(): ?string
    {
        if (Auth::user()->ownerId() === null) {
            return null; // super-admin has no owner context to receive registrations
        }

        $token = Setting::get('tenant_reg_token');
        if (! $token) {
            $token = Str::random(24);
            Setting::set('tenant_reg_token', $token, 'registration');
        }

        return url('/daftar/' . $token);
    }

    public function toggleShare(): void
    {
        $this->showShare = ! $this->showShare;
    }

    public function reject(int $id): void
    {
        $reg = TenantRegistration::findOrFail($id);
        if (! $reg->isPending()) {
            return;
        }

        $reg->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);

        $this->notice = 'Pendaftaran ditolak.';
    }

    public function approve(int $id): void
    {
        $reg = TenantRegistration::findOrFail($id);
        $this->authorize('create', Tenant::class);

        if (! $reg->isPending()) {
            return;
        }

        // Prevent duplicate tenants by NIK / email.
        if ($reg->nik && Tenant::withoutGlobalScopes()->where('nik', $reg->nik)->exists()) {
            $this->notice = 'Gagal: NIK sudah terdaftar sebagai penyewa.';
            return;
        }

        $ownerId = $reg->owner_id;
        $plainPassword = null;

        // Create (or link) a login account for this tenant.
        $user = $reg->email ? User::where('email', $reg->email)->first() : null;
        if (! $user && $reg->email) {
            $plainPassword = Str::password(10, true, true, false);
            $user = User::create([
                'name' => $reg->name,
                'email' => $reg->email,
                'phone' => $reg->phone,
                'password' => Hash::make($plainPassword),
                'owner_id' => $ownerId,
                'email_verified_at' => now(),
            ]);
        }
        if ($user) {
            if (! $user->hasRole('tenant')) {
                $user->assignRole('tenant');
            }
            if (is_null($user->owner_id)) {
                $user->owner_id = $ownerId;
                $user->save();
            }
        }

        $tenant = Tenant::create([
            'owner_id' => $ownerId,
            'name' => $reg->name,
            'email' => $reg->email,
            'nik' => $reg->nik,
            'phone' => $reg->phone,
            'emergency_contact' => $reg->emergency_contact,
            'ktp_photo' => $reg->ktp_photo,
            'status' => 'active',
            'user_id' => $user?->id,
            'created_by' => Auth::user()->name,
        ]);

        $reg->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'tenant_id' => $tenant->id,
        ]);

        if ($plainPassword) {
            $this->sendCredentials($reg->name, $reg->email, $reg->phone, $plainPassword);
            $message = "Disetujui — penyewa & akun login dibuat, kredensial dikirim. (Email: {$reg->email} · Password: {$plainPassword})";
        } else {
            $message = 'Disetujui — penyewa ditambahkan.';
        }

        $this->notice = $message;
    }

    /** Send login credentials to the new tenant (best-effort). */
    private function sendCredentials(string $name, string $email, string $phone, string $plainPassword): void
    {
        $loginUrl = route('login');

        try {
            if (config('mail.default') !== 'log' && $email) {
                Mail::to($email)->send(new TenantWelcomeMail($name, $email, $plainPassword, $loginUrl));
            }
        } catch (\Throwable $e) {
            Log::error('Registration welcome email failed', ['email' => $email, 'error' => $e->getMessage()]);
        }

        try {
            if ($phone) {
                $waMessage = "Halo {$name},\n\n"
                    . "Selamat datang di *Living Kost*! Akun login Anda telah dibuat.\n\n"
                    . "Email: {$email}\n"
                    . "Password: {$plainPassword}\n\n"
                    . "Login: {$loginUrl}\n\n"
                    . "Demi keamanan, segera ganti password setelah login pertama.";
                WhatsAppService::send($phone, $waMessage);
            }
        } catch (\Throwable $e) {
            Log::error('Registration welcome WhatsApp failed', ['phone' => $phone, 'error' => $e->getMessage()]);
        }
    }

    public function render()
    {
        $registrations = TenantRegistration::query()
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(10);

        $pendingCount = TenantRegistration::where('status', 'pending')->count();

        return view('livewire.tenant.registration-index', [
            'registrations' => $registrations,
            'pendingCount' => $pendingCount,
        ]);
    }
}
