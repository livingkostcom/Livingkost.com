<?php

namespace App\Livewire\Public;

use App\Models\OwnerWallet;
use App\Models\Setting;
use App\Models\TenantRegistration;
use App\Models\User;
use App\Services\DokuService;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.guest')]
class TenantRegistrationForm extends Component
{
    use WithFileUploads;

    public string $token = '';
    public int $ownerId;
    public string $kosName = '';
    public bool $submitted = false;
    public bool $paidReturn = false;
    public bool $dpUnavailableNotice = false;

    // DP context (derived in mount)
    public float $dpAmount = 0;
    public string $dpMode = 'none'; // none | online | manual
    public string $bankName = '';
    public string $bankNumber = '';
    public string $bankHolder = '';

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $nik = '';
    public string $emergency_contact = '';
    public $ktp_photo = null;
    public $dp_proof = null;
    public string $note = '';

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:20',
            'nik' => 'required|string|digits:16',
            'emergency_contact' => 'nullable|string|max:100',
            'ktp_photo' => 'nullable|mimes:jpeg,jpg,png,webp,avif|max:2048',
            'note' => 'nullable|string|max:500',
        ];

        // Manual DP requires a transfer proof upload.
        if ($this->dpMode === 'manual') {
            $rules['dp_proof'] = 'required|mimes:jpeg,jpg,png,webp,avif|max:2048';
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'nik.digits' => 'NIK harus 16 angka.',
            'email.required' => 'Email wajib diisi (untuk akun login penyewa).',
            'dp_proof.required' => 'Bukti transfer DP wajib diunggah.',
        ];
    }

    public function mount(string $token)
    {
        $this->token = $token;

        $setting = Setting::where('key', 'tenant_reg_token')
            ->where('value', $token)
            ->whereNotNull('owner_id')
            ->first();

        abort_if(! $setting, 404, 'Tautan pendaftaran tidak ditemukan atau sudah tidak berlaku.');

        $this->ownerId = (int) $setting->owner_id;
        $this->kosName = Setting::getForOwner('app_name', $this->ownerId, 'Living Kost');

        // Payment-return landing (DOKU redirects the customer back here).
        if (request('status') === 'paid') {
            $this->paidReturn = true;
        }

        // Determine the DP flow for this owner.
        $this->dpAmount = (float) Setting::getForOwner('dp_amount', $this->ownerId, 0);
        if ($this->dpAmount > 0) {
            $this->dpMode = OwnerWallet::onlineEnabledFor($this->ownerId) ? 'online' : 'manual';
            if ($this->dpMode === 'manual') {
                $this->bankName = (string) Setting::getForOwner('bank_name', $this->ownerId, '');
                $this->bankNumber = (string) Setting::getForOwner('bank_account_number', $this->ownerId, '');
                $this->bankHolder = (string) Setting::getForOwner('bank_account_holder', $this->ownerId, '');
            }
        }
    }

    public function submit()
    {
        $data = $this->validate();

        $ktpPath = $this->ktp_photo ? $this->ktp_photo->store('ktp-photos', 'public') : null;
        $dpProofPath = ($this->dpMode === 'manual' && $this->dp_proof)
            ? $this->dp_proof->store('dp-proofs', 'public')
            : null;

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
            'dp_amount' => $this->dpMode !== 'none' ? $this->dpAmount : null,
            'dp_method' => $this->dpMode !== 'none' ? $this->dpMode : null,
            'dp_status' => match ($this->dpMode) {
                'online' => 'unpaid',
                'manual' => 'submitted',
                default => null,
            },
            'dp_proof' => $dpProofPath,
        ]);

        // Online DP: create a DOKU payment and send the customer there.
        if ($this->dpMode === 'online') {
            $redirect = $this->startDokuPayment($registration);
            if ($redirect) {
                $this->notifyOwner($registration);
                return redirect()->away($redirect);
            }
            // Gateway unavailable — keep the registration, tell them they'll be contacted.
            $this->dpUnavailableNotice = true;
        }

        $this->notifyOwner($registration);
        $this->submitted = true;
    }

    /** Create the DOKU checkout for the DP; returns the hosted payment URL or null. */
    private function startDokuPayment(TenantRegistration $registration): ?string
    {
        try {
            $doku = new DokuService();
            if (! $doku->isConfigured()) {
                return null;
            }

            $reference = 'DPREG-' . $registration->id . '-' . Str::upper(Str::random(5));
            $callback = url('/daftar/' . $this->token . '?status=paid');

            $res = $doku->createCheckoutPayment(
                $reference,
                $this->dpAmount,
                $registration->name,
                $registration->email,
                $callback
            );

            if (empty($res['success']) || empty($res['url'])) {
                Log::warning('Registration DP DOKU checkout failed', ['reg' => $registration->id, 'res' => $res['error'] ?? null]);
                return null;
            }

            $registration->update(['dp_reference' => $reference]);

            return $res['url'];
        } catch (\Throwable $e) {
            Log::error('Registration DP DOKU error', ['reg' => $registration->id, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /** Best-effort WhatsApp notification to the owner. */
    private function notifyOwner(TenantRegistration $registration): void
    {
        try {
            $owner = User::find($this->ownerId);
            $phone = $owner?->phone ?: Setting::getForOwner('app_phone', $this->ownerId, '');
            if (! $phone) {
                return;
            }

            $message = "*Pendaftaran Penyewa Baru*\n\n"
                . "Nama: {$registration->name}\n"
                . "No. HP: {$registration->phone}\n";
            if ($registration->dp_method === 'manual') {
                $message .= "DP: menunggu verifikasi bukti transfer\n";
            } elseif ($registration->dp_method === 'online') {
                $message .= "DP: menunggu pembayaran online\n";
            }
            $message .= "\nTinjau di menu *Pendaftaran* dashboard Living Kost Anda.";

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
