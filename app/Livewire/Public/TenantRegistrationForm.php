<?php

namespace App\Livewire\Public;

use App\Models\Setting;
use App\Models\TenantRegistration;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.guest')]
class TenantRegistrationForm extends Component
{
    use WithFileUploads;

    public int $ownerId;
    public string $kosName = '';
    public bool $submitted = false;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $nik = '';
    public string $emergency_contact = '';
    public $ktp_photo = null;
    public string $note = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:20',
            'nik' => 'required|string|digits:16',
            'emergency_contact' => 'nullable|string|max:100',
            'ktp_photo' => 'nullable|mimes:jpeg,jpg,png,webp,avif|max:2048',
            'note' => 'nullable|string|max:500',
        ];
    }

    protected function messages(): array
    {
        return [
            'nik.digits' => 'NIK harus 16 angka.',
            'email.required' => 'Email wajib diisi (untuk akun login penyewa).',
        ];
    }

    public function mount(string $token)
    {
        // Resolve the owner this registration link belongs to via their token.
        $setting = Setting::where('key', 'tenant_reg_token')
            ->where('value', $token)
            ->whereNotNull('owner_id')
            ->first();

        abort_if(! $setting, 404, 'Tautan pendaftaran tidak ditemukan atau sudah tidak berlaku.');

        $this->ownerId = (int) $setting->owner_id;
        $this->kosName = Setting::getForOwner('app_name', $this->ownerId, 'Living Kost');
    }

    public function submit()
    {
        $data = $this->validate();

        $ktpPath = null;
        if ($this->ktp_photo) {
            $ktpPath = $this->ktp_photo->store('ktp-photos', 'public');
        }

        $registration = TenantRegistration::create([
            'owner_id' => $this->ownerId,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'nik' => $this->nik,
            'emergency_contact' => $this->emergency_contact ?: null,
            'ktp_photo' => $ktpPath,
            'note' => $this->note ?: null,
            'status' => 'pending',
        ]);

        $this->notifyOwner($registration);

        $this->submitted = true;
    }

    /** Best-effort WhatsApp notification to the owner — never blocks submission. */
    private function notifyOwner(TenantRegistration $registration): void
    {
        try {
            $owner = User::find($this->ownerId);
            $phone = $owner?->phone ?: Setting::getForOwner('app_phone', $this->ownerId, '');
            if (! $phone) {
                return;
            }

            $message = "*Pendaftaran Penyewa Baru*\n\n"
                . "Ada calon penyewa mengisi formulir pendaftaran:\n"
                . "Nama: {$registration->name}\n"
                . "No. HP: {$registration->phone}\n\n"
                . "Tinjau & setujui di menu *Pendaftaran* pada dashboard Living Kost Anda.";

            WhatsAppService::send($phone, $message);
        } catch (\Throwable $e) {
            Log::warning('Tenant registration owner notification failed', [
                'owner_id' => $this->ownerId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.public.tenant-registration-form');
    }
}
