<?php
// --- Dynamic kost detail (read-only from DB) ---
$lk_kost = null;
$lk_roomtypes = [];
$lk_available = 0;
$lk_waPhone = '6285161180441';

$lk_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$lk_envPath = __DIR__ . '/.env';

if ($lk_id > 0 && is_readable($lk_envPath)) {
    $lk_env = [];
    foreach (file($lk_envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $lk_line) {
        $lk_line = trim($lk_line);
        if ($lk_line === '' || $lk_line[0] === '#' || strpos($lk_line, '=') === false) {
            continue;
        }
        list($lk_k, $lk_v) = explode('=', $lk_line, 2);
        $lk_v = trim($lk_v);
        if (strlen($lk_v) >= 2 && ($lk_v[0] === '"' || $lk_v[0] === "'") && substr($lk_v, -1) === $lk_v[0]) {
            $lk_v = substr($lk_v, 1, -1);
        }
        $lk_env[trim($lk_k)] = $lk_v;
    }
    if (!empty($lk_env['DB_DATABASE'])) {
        try {
            $pdo = new PDO(
                'mysql:host=' . ($lk_env['DB_HOST'] ?? 'localhost') . ';port=' . ($lk_env['DB_PORT'] ?? '3306') . ';dbname=' . $lk_env['DB_DATABASE'] . ';charset=utf8mb4',
                $lk_env['DB_USERNAME'] ?? '',
                $lk_env['DB_PASSWORD'] ?? '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT, PDO::ATTR_TIMEOUT => 3]
            );
            $st = $pdo->prepare("SELECT id, name, address, description, location_label, badge_text, featured_image, gallery, owner_id, gender_type, common_facilities
                                 FROM properties WHERE id = ? AND is_featured = 1 AND status = 'active' LIMIT 1");
            $st->execute([$lk_id]);
            $lk_kost = $st->fetch(PDO::FETCH_ASSOC) ?: null;

            if ($lk_kost) {
                $st2 = $pdo->prepare("SELECT id, name, price, facilities FROM room_types WHERE property_id = ? ORDER BY price ASC");
                $st2->execute([$lk_id]);
                $lk_roomtypes = $st2->fetchAll(PDO::FETCH_ASSOC) ?: [];

                $st3 = $pdo->prepare("SELECT COUNT(*) FROM rooms r JOIN room_types rt ON r.room_type_id = rt.id
                                      WHERE rt.property_id = ? AND r.status = 'available'
                                      AND NOT EXISTS (SELECT 1 FROM leases l WHERE l.room_id = r.id AND l.status = 'active')");
                $st3->execute([$lk_id]);
                $lk_available = (int) $st3->fetchColumn();

                $st4 = $pdo->prepare("SELECT value FROM settings WHERE `key` = 'app_phone' AND (owner_id = ? OR owner_id IS NULL)
                                      ORDER BY (owner_id IS NULL) ASC LIMIT 1");
                $st4->execute([$lk_kost['owner_id']]);
                $ph = $st4->fetchColumn();
                if ($ph) {
                    $ph = preg_replace('/[^0-9]/', '', $ph);
                    if (strpos($ph, '0') === 0) {
                        $ph = '62' . substr($ph, 1);
                    }
                    if ($ph !== '') {
                        $lk_waPhone = $ph;
                    }
                }
            }
        } catch (\Throwable $e) {
            $lk_kost = null;
        }
    }
}

if (!$lk_kost) {
    http_response_code(404);
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Kost tidak ditemukan | Living Kost</title><script src="https://cdn.tailwindcss.com"></script></head><body class="min-h-screen flex items-center justify-center bg-gray-50 p-6"><div class="text-center"><div class="text-2xl font-bold text-orange-600 mb-2">Living<span class="text-gray-900">Kost</span></div><h1 class="text-xl font-bold text-gray-800 mb-2">Kost tidak ditemukan</h1><p class="text-gray-500 mb-6">Listing ini mungkin sudah tidak tersedia.</p><a href="/" class="inline-block bg-orange-600 text-white px-6 py-3 rounded-full font-semibold hover:bg-orange-700 transition">Kembali ke Beranda</a></div></body></html>';
    exit;
}

// Helpers + derived data
function lk_rp($n)
{
    return $n !== null ? 'Rp ' . number_format((float) $n, 0, ',', '.') : 'Hubungi kami';
}
function lk_fa_icon($label)
{
    $l = strtolower($label);
    $map = [
        'ac' => 'fa-snowflake', 'pendingin' => 'fa-snowflake',
        'wifi' => 'fa-wifi', 'internet' => 'fa-wifi',
        'kasur' => 'fa-bed', 'bed' => 'fa-bed', 'tidur' => 'fa-bed',
        'mandi' => 'fa-shower', 'shower' => 'fa-shower',
        'kloset' => 'fa-toilet', 'toilet' => 'fa-toilet', 'wc' => 'fa-toilet',
        'meja' => 'fa-briefcase', 'kerja' => 'fa-briefcase',
        'lemari' => 'fa-box-archive',
        'tv' => 'fa-tv',
        'dapur' => 'fa-utensils',
        'parkir' => 'fa-square-parking', 'garasi' => 'fa-square-parking',
        'cuci' => 'fa-soap',
        'air' => 'fa-droplet', 'kulkas' => 'fa-snowflake',
    ];
    foreach ($map as $kw => $icon) {
        if (strpos($l, $kw) !== false) {
            return $icon;
        }
    }
    return 'fa-check';
}

$lk_facilities = [];
foreach ($lk_roomtypes as $rt) {
    $f = json_decode($rt['facilities'] ?? '[]', true);
    if (is_array($f)) {
        foreach ($f as $x) {
            $x = trim($x);
            if ($x !== '' && !in_array($x, $lk_facilities, true)) {
                $lk_facilities[] = $x;
            }
        }
    }
}

$lk_common_facilities = json_decode($lk_kost['common_facilities'] ?? '[]', true);
if (!is_array($lk_common_facilities)) {
    $lk_common_facilities = [];
}

$lk_gender_type = $lk_kost['gender_type'] ?? '';
$lk_gender_label = match($lk_gender_type) {
    'putra' => 'Putra',
    'putri' => 'Putri',
    'putra_putri' => 'Putra & Putri',
    default => '',
};

$lk_minPrice = null;
foreach ($lk_roomtypes as $rt) {
    if ($rt['price'] !== null) {
        $p = (float) $rt['price'];
        if ($lk_minPrice === null || $p < $lk_minPrice) {
            $lk_minPrice = $p;
        }
    }
}

// Gallery: cover (featured_image) first, then additional gallery photos
$lk_gallery = [];
if (!empty($lk_kost['featured_image'])) {
    $lk_gallery[] = '/images/' . $lk_kost['featured_image'];
}
$lk_g = json_decode($lk_kost['gallery'] ?? '[]', true);
if (is_array($lk_g)) {
    foreach ($lk_g as $lk_gp) {
        $lk_gp = trim((string) $lk_gp);
        if ($lk_gp !== '') {
            $lk_gallery[] = '/images/' . $lk_gp;
        }
    }
}
if (empty($lk_gallery)) {
    $lk_gallery[] = 'https://placehold.co/1200x700/f97316/ffffff?text=Living+Kost';
}
$lk_hero = $lk_gallery[0];

$e = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $e($lk_kost['name']) ?> | Detail Kost</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sticky-card { top: 100px; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="sticky top-0 z-50 bg-white shadow-sm px-6 py-4 flex justify-between items-center">
        <a href="/" class="text-2xl font-bold text-orange-600">Living<span class="text-gray-900">Kost</span></a>
        <div class="hidden md:flex items-center space-x-6">
            <button class="bg-orange-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-orange-700 transition"><a href="/mitra" class="hover:text-orange-600 transition">Jadi Mitra</a></button>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 md:px-6 py-8">
        <nav class="text-sm text-gray-500 mb-6">
            <a href="/" class="hover:text-orange-600">Home</a> /
            <?php if (!empty($lk_kost['location_label'])): ?>
                <span class="hover:text-orange-600"><?= $e($lk_kost['location_label']) ?></span> /
            <?php endif; ?>
            <span class="text-gray-900 font-semibold"><?= $e($lk_kost['name']) ?></span>
        </nav>

        <!-- Gallery -->
        <div class="max-w-7xl mx-auto mb-10">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative h-[300px] md:h-[550px] overflow-hidden rounded-2xl shadow-sm bg-gray-100">
                        <img id="mainView" src="<?= $e($lk_gallery[0]) ?>"
                             class="w-full h-full object-cover cursor-zoom-in transition-all duration-500"
                             onclick="openGallery(this.src)" alt="<?= $e($lk_kost['name']) ?>">
                        <?php if (count($lk_gallery) > 1): ?>
                            <div class="absolute bottom-4 right-4 bg-black/60 backdrop-blur-md text-white px-3 py-1 rounded-full text-xs">
                                <span id="currentImgIdx">1</span> / <?= count($lk_gallery) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (count($lk_gallery) > 1): ?>
                    <div class="flex lg:flex-col overflow-x-auto lg:overflow-y-auto gap-3 no-scrollbar lg:w-32 lg:h-[550px] py-1">
                        <?php foreach ($lk_gallery as $idx => $img): ?>
                            <div class="min-w-[100px] lg:min-w-full h-24 cursor-pointer rounded-xl overflow-hidden border-2 thumb-item shrink-0 transition <?= $idx === 0 ? 'border-orange-600 opacity-100' : 'border-transparent opacity-60 hover:opacity-100' ?>"
                                 onclick="changeImage(this, '<?= $e($img) ?>', <?= $idx + 1 ?>)">
                                <img src="<?= $e($img) ?>" class="w-full h-full object-cover">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <style>
            .no-scrollbar::-webkit-scrollbar { display: none; }
            .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        </style>

        <div id="galleryModal" class="fixed inset-0 z-[100] hidden bg-black/90 items-center justify-center p-4 backdrop-blur-sm" style="display:none;">
            <button onclick="closeGallery()" class="absolute top-6 right-6 text-white text-4xl hover:text-orange-500 transition">&times;</button>
            <img id="modalImage" src="" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl">
        </div>

        <div class="flex flex-col md:flex-row gap-10">
            <div class="flex-1">
                <!-- Title + badges -->
                <div class="border-b pb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2"><?= $e($lk_kost['name']) ?></h1>
                    <p class="text-gray-500 mb-4"><i class="fas fa-location-dot text-orange-600 mr-2"></i><?= $e($lk_kost['address']) ?></p>
                    <div class="flex flex-wrap gap-2">
                        <?php if ($lk_gender_label): ?>
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                                <?= $lk_gender_type === 'putra' ? 'bg-blue-50 text-blue-600' : ($lk_gender_type === 'putri' ? 'bg-pink-50 text-pink-600' : 'bg-purple-50 text-purple-600') ?>">
                                <?= $e($lk_gender_label) ?>
                            </span>
                        <?php endif; ?>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide <?= $lk_available > 0 ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500' ?>">
                            <?= $lk_available > 0 ? 'Tersedia ' . $lk_available . ' Kamar' : 'Belum ada kamar tersedia' ?>
                        </span>
                    </div>
                </div>

                <!-- Description -->
                <?php if (!empty($lk_kost['description'])): ?>
                    <div class="py-10 border-b">
                        <h2 class="text-xl font-bold mb-4">Tentang Kost</h2>
                        <p class="text-gray-600 leading-relaxed whitespace-pre-line"><?= $e($lk_kost['description']) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Fasilitas Umum -->
                <?php if (!empty($lk_common_facilities)): ?>
                    <div class="py-10 border-b">
                        <h2 class="text-xl font-bold mb-6">Fasilitas Umum</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                            <?php foreach ($lk_common_facilities as $f): ?>
                                <div class="flex items-center text-gray-700">
                                    <i class="fas <?= $e(lk_fa_icon($f)) ?> w-8 text-orange-500"></i>
                                    <span><?= $e($f) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Fasilitas Kamar -->
                <?php if (!empty($lk_facilities)): ?>
                    <div class="py-10 border-b">
                        <h2 class="text-xl font-bold mb-6">Fasilitas Kamar</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                            <?php foreach ($lk_facilities as $f): ?>
                                <div class="flex items-center text-gray-700">
                                    <i class="fas <?= $e(lk_fa_icon($f)) ?> w-8 text-orange-500"></i>
                                    <span><?= $e($f) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Room types -->
                <div class="py-10 border-b">
                    <h2 class="text-xl font-bold mb-6">Pilihan Tipe Kamar</h2>
                    <?php if (!empty($lk_roomtypes)): ?>
                        <div class="space-y-6">
                            <?php foreach ($lk_roomtypes as $rt):
                                $rtFacs = json_decode($rt['facilities'] ?? '[]', true);
                                if (!is_array($rtFacs)) { $rtFacs = []; }
                            ?>
                                <div class="flex flex-col md:flex-row gap-6 items-start border border-gray-100 rounded-2xl p-4 shadow-sm">
                                    <div class="w-full md:w-1/3 h-44 rounded-xl overflow-hidden shrink-0 bg-gray-100">
                                        <img src="<?= $e($lk_hero) ?>" class="w-full h-full object-cover cursor-pointer hover:opacity-90 transition" onclick="openGallery(this.src)">
                                    </div>
                                    <div class="flex-1 pt-1">
                                        <h3 class="text-lg font-bold text-gray-900 mb-2"><?= $e($rt['name']) ?></h3>
                                        <?php if (!empty($rtFacs)): ?>
                                            <div class="flex flex-wrap gap-2 mb-3">
                                                <?php foreach (array_slice($rtFacs, 0, 6) as $f): ?>
                                                    <span class="inline-flex items-center gap-1 bg-orange-50 text-orange-700 text-xs px-2 py-1 rounded-lg">
                                                        <i class="fas <?= $e(lk_fa_icon($f)) ?>"></i> <?= $e($f) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase">Harga</span>
                                            <span class="text-orange-600 text-base font-bold"><?= $e(lk_rp($rt['price'])) ?><span class="text-gray-400 font-normal text-sm">/bln</span></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500">Informasi tipe kamar belum tersedia. Hubungi kami untuk detail.</p>
                    <?php endif; ?>
                </div>

                <!-- Location -->
                <div class="py-10 border-b">
                    <h2 class="text-xl font-bold mb-4">Lokasi Kost</h2>
                    <p class="text-gray-600 mb-4"><i class="fas fa-location-dot text-orange-600 mr-2"></i><?= $e($lk_kost['address']) ?></p>
                    <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($lk_kost['address']) ?>" target="_blank"
                       class="inline-flex items-center gap-2 text-orange-600 font-semibold hover:text-orange-700">
                        <i class="fas fa-map"></i> Lihat di Google Maps
                    </a>
                </div>
            </div>

            <!-- Booking sidebar -->
            <div class="w-full md:w-80 lg:w-96">
                <div class="sticky sticky-card bg-white border border-gray-100 rounded-3xl shadow-2xl p-8">
                    <p class="text-sm text-gray-500 mb-1">Mulai dari</p>
                    <div class="flex items-end space-x-2 mb-6">
                        <span id="displayPrice" class="text-3xl font-bold text-gray-900"><?= $e(lk_rp($lk_minPrice)) ?></span>
                        <span class="text-gray-500 pb-1">/ bulan</span>
                    </div>

                    <?php if (!empty($lk_roomtypes)): ?>
                        <div class="space-y-4 mb-6">
                            <div class="p-3 border rounded-xl bg-gray-50">
                                <label class="block text-[10px] font-bold uppercase text-gray-400">Tipe Kamar</label>
                                <select id="roomSelect" class="w-full bg-transparent outline-none text-sm font-semibold cursor-pointer">
                                    <?php foreach ($lk_roomtypes as $rt): ?>
                                        <option value="<?= $e($rt['name']) ?>"><?= $e($rt['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="flex flex-col gap-4">
                        <a id="waButton" href="#" target="_blank" class="inline-flex items-center justify-center bg-green-500 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-green-600 transition shadow-lg shadow-green-900/20 w-full">
                            <i class="fab fa-whatsapp text-2xl mr-3"></i>
                            Tanya Ketersediaan
                        </a>
                    </div>

                    <div class="mt-6 pt-6 border-t text-center">
                        <p class="text-xs text-gray-400 font-medium"><i class="fas fa-shield-halved mr-2"></i>Pembayaran Aman &amp; Terverifikasi</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-900 text-white py-12 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <div class="text-2xl font-bold text-orange-500 mb-4">Living Kost</div>
                <p class="text-gray-400 max-w-sm">Solusi hunian modern untuk anak muda di Indonesia yang mencari kenyamanan dan koneksi.</p>
            </div>
            <div>
                <h5 class="font-bold mb-4">Pusat Bantuan</h5>
                <ul class="text-gray-400 space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white">Syarat &amp; Ketentuan</a></li>
                    <li><a href="#" class="hover:text-white">Kebijakan Privasi</a></li>
                    <li><a href="#" class="hover:text-white">Hubungi Kami</a></li>
                </ul>
            </div>
            <div>
                <h5 class="font-bold mb-4">Ikuti Kami</h5>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-tiktok"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white text-xl"><i class="fab fa-facebook"></i></a>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto border-t border-gray-800 mt-12 pt-8 text-center text-gray-500 text-sm">
            &copy; 2024 - <?php echo date('Y'); ?> Living Kost Indonesia. All rights reserved.
        </div>
    </footer>

    <script>
        const roomSelect = document.getElementById('roomSelect');
        const displayPrice = document.getElementById('displayPrice');
        const waButton = document.getElementById('waButton');
        const phoneNumber = <?= json_encode($lk_waPhone) ?>;
        const kostName = <?= json_encode($lk_kost['name']) ?>;
        const roomData = {
            <?php foreach ($lk_roomtypes as $rt): ?>
            <?= json_encode($rt['name']) ?>: <?= json_encode(lk_rp($rt['price'])) ?>,
            <?php endforeach; ?>
        };

        function updateContent() {
            const selectedRoom = roomSelect ? roomSelect.value : '';
            const selectedPrice = selectedRoom && roomData[selectedRoom] ? roomData[selectedRoom] : <?= json_encode(lk_rp($lk_minPrice)) ?>;
            if (displayPrice && selectedRoom) displayPrice.innerText = selectedPrice;

            let message;
            if (selectedRoom) {
                message = `Halo admin, saya tertarik dengan ${kostName} (tipe ${selectedRoom}, ${selectedPrice}). Apakah masih tersedia?`;
            } else {
                message = `Halo admin, saya tertarik dengan ${kostName}. Apakah masih tersedia?`;
            }
            waButton.href = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
        }

        if (roomSelect) roomSelect.addEventListener('change', updateContent);
        updateContent();
    </script>
    <script>
        function changeImage(element, src, idx) {
            const mainImg = document.getElementById('mainView');
            mainImg.style.opacity = '0.5';
            setTimeout(() => { mainImg.src = src; mainImg.style.opacity = '1'; }, 150);
            document.querySelectorAll('.thumb-item').forEach(t => {
                t.classList.remove('border-orange-600', 'opacity-100');
                t.classList.add('border-transparent', 'opacity-60');
            });
            element.classList.remove('border-transparent', 'opacity-60');
            element.classList.add('border-orange-600', 'opacity-100');
            const c = document.getElementById('currentImgIdx');
            if (c) c.innerText = idx;
            element.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }

        function openGallery(src) {
            const modal = document.getElementById('galleryModal');
            document.getElementById('modalImage').src = src;
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeGallery() {
            const modal = document.getElementById('galleryModal');
            modal.style.display = 'none';
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        document.getElementById('galleryModal').addEventListener('click', function (e) {
            if (e.target === this) closeGallery();
        });
    </script>
</body>
</html>
