<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::role('owner')->first();
        if (!$owner) {
            return;
        }

        $properties = Property::all();
        $firstPropertyId = $properties->first()?->id;

        $announcements = [
            [
                'title' => 'Jadwal Fumigasi Rutin',
                'content' => "Diberitahukan kepada seluruh penghuni kos bahwa akan dilaksanakan fumigasi rutin pada:\n\nHari/Tanggal: Sabtu, 15 Maret 2026\nWaktu: 08.00 - 12.00 WIB\n\nMohon untuk:\n1. Menutup semua makanan dan minuman\n2. Menutup akuarium (jika ada)\n3. Mengamankan hewan peliharaan\n4. Tidak berada di kamar selama proses fumigasi\n\nTerima kasih atas kerjasamanya.",
                'priority' => 'important',
                'target' => 'all',
                'property_id' => null,
                'published_at' => '2026-03-08',
                'expires_at' => '2026-03-16',
            ],
            [
                'title' => 'Pemadaman Listrik PLN',
                'content' => "Informasi dari PLN bahwa akan ada pemadaman listrik bergilir pada:\n\nTanggal: Minggu, 10 Maret 2026\nWaktu: 09.00 - 15.00 WIB\n\nMohon siapkan penerangan darurat dan charge perangkat elektronik Anda sebelumnya.\n\nTerima kasih.",
                'priority' => 'urgent',
                'target' => 'all',
                'property_id' => null,
                'published_at' => '2026-03-07',
                'expires_at' => '2026-03-11',
            ],
            [
                'title' => 'Perbaikan Pipa Air Lantai 2',
                'content' => "Diberitahukan kepada penghuni lantai 2 bahwa akan dilakukan perbaikan pipa air pada:\n\nTanggal: Senin, 12 Maret 2026\nWaktu: 10.00 - 14.00 WIB\n\nSelama perbaikan, air di lantai 2 akan dimatikan sementara. Mohon siapkan persediaan air.\n\nMohon maaf atas ketidaknyamanannya.",
                'priority' => 'important',
                'target' => 'property',
                'property_id' => $firstPropertyId,
                'published_at' => '2026-03-06',
                'expires_at' => '2026-03-13',
            ],
            [
                'title' => 'Peraturan Jam Malam Diperbarui',
                'content' => "Diberitahukan bahwa mulai 1 April 2026, jam malam akan diperbarui:\n\n- Pintu gerbang ditutup pukul 23.00 WIB (dari sebelumnya 22.00 WIB)\n- Tamu terakhir diperbolehkan sampai pukul 21.00 WIB\n- Penghuni yang pulang setelah jam 23.00 wajib menghubungi pengelola\n\nPeraturan ini berlaku untuk kenyamanan bersama. Terima kasih atas pengertiannya.",
                'priority' => 'normal',
                'target' => 'all',
                'property_id' => null,
                'published_at' => '2026-03-01',
                'expires_at' => null,
            ],
            [
                'title' => 'Pembayaran Kos Bulan Maret',
                'content' => "Mengingatkan kepada seluruh penghuni kos untuk segera melakukan pembayaran sewa bulan Maret 2026 paling lambat tanggal 10 Maret 2026.\n\nPembayaran bisa dilakukan melalui transfer bank ke rekening yang tertera di invoice.\n\nBagi yang sudah membayar, mohon abaikan pesan ini. Terima kasih.",
                'priority' => 'normal',
                'target' => 'all',
                'property_id' => null,
                'published_at' => '2026-03-01',
                'expires_at' => '2026-03-10',
            ],
            [
                'title' => 'Acara Bersih-Bersih Bersama',
                'content' => "Hai penghuni kos! 🧹\n\nKami mengadakan acara bersih-bersih bersama:\n\nTanggal: Sabtu, 22 Maret 2026\nWaktu: 07.00 - 10.00 WIB\n\nKami menyediakan peralatan kebersihan dan snack. Ayo berpartisipasi untuk kenyamanan kita bersama!\n\nYang berminat bisa langsung hadir. Terima kasih! 😊",
                'priority' => 'normal',
                'target' => 'all',
                'property_id' => null,
                'published_at' => '2026-03-05',
                'expires_at' => '2026-03-23',
            ],
            [
                'title' => 'WiFi Maintenance',
                'content' => "Internet/WiFi akan mengalami gangguan sementara karena pemeliharaan rutin:\n\nTanggal: Rabu, 19 Maret 2026\nWaktu: 22.00 - 06.00 WIB (dini hari)\n\nMohon persiapkan data seluler untuk koneksi darurat selama maintenance.\n\nTerima kasih atas pengertiannya.",
                'priority' => 'normal',
                'target' => 'all',
                'property_id' => null,
                'published_at' => '2026-03-15',
                'expires_at' => '2026-03-20',
            ],
        ];

        foreach ($announcements as $data) {
            Announcement::create(array_merge($data, [
                'created_by' => $owner->id,
                'is_active' => true,
            ]));
        }
    }
}
