<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\ResidentDirectory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ResidentDirectoryController extends Controller
{
    public function manage(Request $request)
    {
        $ownerId = $this->managingOwner($request);
        $token = Setting::where('owner_id', $ownerId)->where('key', 'resident_directory_token')->value('value');

        return $this->privateResponse(response()->view('resident-directory.manage', [
            'phones' => implode("\n", ResidentDirectory::phones($ownerId)),
            'shareUrl' => $token ? route('resident-directory.show', $token) : null,
        ]));
    }

    public function save(Request $request)
    {
        $ownerId = $this->managingOwner($request);
        $data = $request->validate(['phones' => 'nullable|string|max:2000']);
        $phones = [];
        foreach (preg_split('/[\r\n,;]+/', $data['phones'] ?? '') as $line) {
            if (trim($line) === '') {
                continue;
            }
            if (! preg_match('/^[+\d\s().-]+$/', trim($line))) {
                throw ValidationException::withMessages(['phones' => 'Masukkan hanya nomor HP, satu nomor per baris.']);
            }
            $phone = ResidentDirectory::normalizePhone($line);
            if (! preg_match('/^628\d{7,11}$/', $phone)) {
                throw ValidationException::withMessages(['phones' => 'Nomor HP harus valid, misalnya 081234567890 atau +6281234567890.']);
            }
            $phones[] = $phone;
        }

        DB::transaction(function () use ($ownerId, $phones) {
            Setting::firstOrCreate(['owner_id' => $ownerId, 'key' => 'resident_directory_token'], ['value' => Str::random(48), 'group' => 'resident_directory']);
            Setting::updateOrCreate(['owner_id' => $ownerId, 'key' => 'resident_directory_phones'], ['value' => json_encode(array_values(array_unique($phones))), 'group' => 'resident_directory']);
            // Updating access revokes all previously opened directory sessions.
            Setting::updateOrCreate(['owner_id' => $ownerId, 'key' => 'resident_directory_version'], ['value' => Str::random(24), 'group' => 'resident_directory']);
        });

        return $this->privateResponse(redirect()->route('resident-directory.manage')->with('directory_saved', 'Nomor pembuka dokumen berhasil disimpan. Sesi sebelumnya sudah ditutup.'));
    }

    public function show(Request $request, string $token)
    {
        $ownerId = $this->ownerForToken($token);
        $opened = $this->hasAccess($request, $ownerId);

        return $this->privateResponse(response()->view('resident-directory.show', [
            'token' => $token,
            'opened' => $opened,
            'residents' => $opened ? ResidentDirectory::residents($ownerId)->get() : collect(),
        ]));
    }

    public function unlock(Request $request, string $token)
    {
        $ownerId = $this->ownerForToken($token);
        $request->validate(['phone' => 'required|string|max:30']);
        $phone = ResidentDirectory::normalizePhone($request->input('phone'));
        if (! in_array($phone, ResidentDirectory::phones($ownerId), true)) {
            $request->session()->forget("resident_directory.{$ownerId}");
            throw ValidationException::withMessages(['phone' => 'Nomor HP tidak terdaftar untuk membuka dokumen ini. Hubungi owner.']);
        }

        $request->session()->regenerate();
        $request->session()->put("resident_directory.{$ownerId}", [
            'phone' => $phone,
            'expires_at' => now()->addMinutes(30)->timestamp,
            'version' => $this->version($ownerId),
        ]);

        return $this->privateResponse(redirect()->route('resident-directory.show', $token));
    }

    public function close(Request $request, string $token)
    {
        $ownerId = $this->ownerForToken($token);
        $request->session()->forget("resident_directory.{$ownerId}");

        return $this->privateResponse(redirect()->route('resident-directory.show', $token));
    }

    public function photo(Request $request, string $token, int $lease)
    {
        $ownerId = $this->ownerForToken($token);
        abort_unless($this->hasAccess($request, $ownerId), 403);
        $resident = ResidentDirectory::residents($ownerId)->where('l.id', $lease)->first();
        abort_unless($resident && $resident->ktp_photo, 404);

        // Do not expose a reusable public storage URL in the directory.
        $root = realpath(storage_path('app/public'));
        $path = realpath(storage_path('app/public/' . $resident->ktp_photo));
        abort_unless($root && $path && str_starts_with($path, $root . DIRECTORY_SEPARATOR) && is_file($path), 404);
        abort_unless(in_array(mime_content_type($path), ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif'], true), 404);

        return $this->privateResponse(response()->file($path));
    }

    private function managingOwner(Request $request): int
    {
        $user = $request->user();
        abort_unless($user?->isOwner() && ! $user->isCoOwnerViewer(), 403);

        return $user->id;
    }

    private function ownerForToken(string $token): int
    {
        abort_unless(preg_match('/^[A-Za-z0-9]{48}$/', $token), 404);
        $ownerId = Setting::where('key', 'resident_directory_token')->where('value', $token)->whereNotNull('owner_id')->value('owner_id');
        abort_unless($ownerId, 404);

        return (int) $ownerId;
    }

    private function version(int $ownerId): ?string
    {
        return Setting::where('owner_id', $ownerId)->where('key', 'resident_directory_version')->value('value');
    }

    private function hasAccess(Request $request, int $ownerId): bool
    {
        $access = $request->session()->get("resident_directory.{$ownerId}");
        $valid = is_array($access)
            && ($access['expires_at'] ?? 0) > now()->timestamp
            && ($access['version'] ?? null) === $this->version($ownerId)
            && in_array($access['phone'] ?? '', ResidentDirectory::phones($ownerId), true);
        if (! $valid) {
            $request->session()->forget("resident_directory.{$ownerId}");
        }

        return $valid;
    }

    private function privateResponse($response)
    {
        $response->headers->set('Cache-Control', 'private, no-store, max-age=0');
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }
}
