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

    /** After an approval, the welcome message + phone for the click-to-send button. */
    public string $welcomePhone = '';
    public string $welcomeMessage = '';
    public string $welcomeName = '';

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

        // Build the WhatsApp welcome message and expose it for the click-to-send
        // button (so the owner can always send it, even without an auto-send gateway).
        $welcome = $this->buildWelcomeMessage($reg->name, $reg->email, $plainPassword);
        $this->welcomeName = $reg->name;
        $this->welcomePhone = $this->normalizePhone($reg->phone);
        $this->welcomeMessage = $welcome;

        if ($plainPassword) {
            $this->sendCredentials($reg->name, $reg->email, $reg->phone, $plainPassword, $welcome);
            $message = "Disetujui — penyewa & akun login dibuat. Kirim pesan selamat datang lewat tombol WhatsApp di bawah. (Email: {$reg->email} · Password: {$plainPassword})";
        } else {
            // Existing account: no new password to show; still offer the welcome message.
            if ($reg->phone) {
                try {
                    WhatsAppService::send($reg->phone, $welcome);
                } catch (\Throwable $e) {
                    Log::error('Registration welcome WhatsApp failed', ['phone' => $reg->phone, 'error' => $e->getMessage()]);
                }
            }
            $message = 'Disetujui — penyewa ditambahkan (akun sudah ada sebelumnya). Kirim pesan selamat datang lewat tombol WhatsApp di bawah.';
        }

        $this->notice = $message;
    }

    /** Normalize an Indonesian phone number to WhatsApp format (62xxxxxxxx). */
    private function normalizePhone(?string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', (string) $phone);
        if ($phone === '') {
            return '';
        }
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (! str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /** The Living Kost WhatsApp welcome message for a newly approved tenant. */
    private function buildWelcomeMessage(string $name, ?string $email, ?string $plainPassword): string
    {
        $passwordLine = $plainPassword
            ? "🔑 *Password:* {$plainPassword}"
            : "🔑 *Password:* (gunakan password akun Anda sebelumnya)";
        $emailLine = $email ?: '(hubungi admin)';

        return "🎉 *SELAMAT DATANG DI LIVING KOST* 🏡\n\n"
            . "Halo Kak {$name} 👋\n\n"
            . "Terima kasih sudah memilih Living Kost sebagai tempat tinggal.\n\n"
            . "📱 *AKSES LAYANAN LIVING KOST*\n\n"
            . "Untuk melakukan *pembayaran, pengaduan, dan melihat pengumuman kost*, silakan login melalui website Living Kost:\n\n"
            . "www.livingkost.com\n\n"
            . "Silakan login menggunakan:\n"
            . "📧 *Email:* {$emailLine}\n"
            . "{$passwordLine}\n\n"
            . "📶 *INFORMASI WI-FI*\n\n"
            . "*Nama Wi-Fi:* living kost (lantai kamu)\n"
            . "*Password:* S3nen\n\n"
            . "👨‍🔧 *KONTAK PENJAGA*\n\n"
            . "*Rudi:* +62 821-1315-5861\n\n"
            . "Jika ada kendala di kamar, fasilitas, atau membutuhkan bantuan selama tinggal di Living Kost, dapat mengisi form pengaduan pada website / langsung menghubungi Pak Rudi.\n\n"
            . "📌 *MOHON DIPERHATIKAN*\n"
            . "• Jaga kebersihan dan kenyamanan bersama\n"
            . "• Patuhi peraturan kost yang berlaku\n"
            . "• Jaga fasilitas yang tersedia dengan baik\n"
            . "• Segera laporkan jika terdapat kerusakan atau kendala\n\n"
            . "Sekali lagi, *selamat datang di Living Kost!* 🤝\n\n"
            . "Semoga betah dan nyaman selama tinggal bersama kami.\n\n"
            . "*Living Kost* 🧡";
    }

    /** Send login credentials + welcome message to the new tenant (best-effort). */
    private function sendCredentials(string $name, string $email, string $phone, string $plainPassword, string $welcomeMessage): void
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
                WhatsAppService::send($phone, $welcomeMessage);
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

        // Prepare a persistent welcome-message + wa.me phone for each approved row,
        // so the owner can (re-)send the welcome message any time. The original
        // password is not recoverable here, so it uses the "existing password" line.
        $welcomeLinks = [];
        foreach ($registrations as $reg) {
            if ($reg->status === 'approved' && $reg->phone) {
                $welcomeLinks[$reg->id] = [
                    'phone' => $this->normalizePhone($reg->phone),
                    'message' => $this->buildWelcomeMessage($reg->name, $reg->email, null),
                ];
            }
        }

        return view('livewire.tenant.registration-index', [
            'registrations' => $registrations,
            'pendingCount' => $pendingCount,
            'welcomeLinks' => $welcomeLinks,
        ]);
    }
}
