<?php
// --- Dynamic "Rekomendasi Kost": featured properties from all owners ---
// Reads the database directly (read-only) so the static landing can show
// listings that owners toggle from their dashboard. Fails silently.
$lk_featured = [];
$lk_envPath = __DIR__ . '/.env';
if (is_readable($lk_envPath)) {
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
            $lk_pdo = new PDO(
                'mysql:host=' . ($lk_env['DB_HOST'] ?? 'localhost') . ';port=' . ($lk_env['DB_PORT'] ?? '3306') . ';dbname=' . $lk_env['DB_DATABASE'] . ';charset=utf8mb4',
                $lk_env['DB_USERNAME'] ?? '',
                $lk_env['DB_PASSWORD'] ?? '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT, PDO::ATTR_TIMEOUT => 3]
            );
            $lk_stmt = $lk_pdo->query(
                "SELECT p.id, p.name, p.location_label, p.featured_image, p.gender_type,
                        (SELECT MIN(rt.price) FROM room_types rt WHERE rt.property_id = p.id) AS price_from,
                        (SELECT rt.facilities FROM room_types rt WHERE rt.property_id = p.id ORDER BY rt.price ASC LIMIT 1) AS facilities,
                        (SELECT COUNT(*) FROM rooms r JOIN room_types rt ON r.room_type_id = rt.id
                         WHERE rt.property_id = p.id AND r.status = 'available'
                         AND NOT EXISTS (SELECT 1 FROM leases l WHERE l.room_id = r.id AND l.status = 'active')) AS available_rooms
                 FROM properties p
                 WHERE p.is_featured = 1 AND p.status = 'active'
                 ORDER BY p.updated_at DESC
                 LIMIT 6"
            );
            if ($lk_stmt) {
                $lk_featured = $lk_stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (\Throwable $e) {
            $lk_featured = [];
        }
    }
}

// Map a facility label to a Font Awesome icon (kept in sync with detail.php).
function lk_fa_icon($label)
{
    $l = strtolower(trim($label));
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Living Kost | Hunian Eksklusif & Nyaman</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-bg {
            background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?ixlib=rb-4.0.3&auto=format&fit=crop&w=1471&q=80');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="sticky top-0 z-50 bg-white shadow-sm px-6 py-4 flex justify-between items-center">
        <div class="text-2xl font-bold text-orange-600"><a href="/">Living<span class="text-gray-900">Kost</span></a></div>
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="/login" class="text-gray-700 hover:text-orange-600 font-semibold px-3 sm:px-4 py-2 rounded-full transition">Login</a>
            <a href="/mitra" class="bg-orange-600 text-white px-4 sm:px-6 py-2 rounded-full font-semibold hover:bg-orange-700 transition whitespace-nowrap">Jadi Mitra</a>
        </div>
    </nav>

    <header class="hero-bg h-[85vh] flex flex-col justify-center items-center text-center px-4">
        <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-4">Kost Nyaman, Senyaman Di Rumah</h1>
        <p class="text-lg md:text-xl text-white opacity-90 mb-8 max-w-2xl">Hunian eksklusif dengan fasilitas lengkap di lokasi paling strategis</p>
        
    </header>

    <section class="py-20 px-6 max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12">Kenapa Pilih Living Kost?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6">
                <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bed text-orange-600 text-2xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">Fully Furnished</h3>
                <p class="text-gray-500 text-sm">Kamar rapih lengkap dengan AC, kasur, lemari, meja kerja dan kamar mandi dalam.</p>
            </div>
            <div class="text-center p-6">
                <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-wifi text-orange-600 text-2xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">Fasilitas Umum Gratis</h3>
                <p class="text-gray-500 text-sm">Wifi gratis, dapur lengkap gas gratis, kulkas, dispenser, mesin cuci, jemuran dan parkiran motor.</p>
            </div>
            <div class="text-center p-6">
                <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-building text-orange-600 text-2xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">Akses 24 Jam</h3>
                <p class="text-gray-500 text-sm">Keamanan dilengkapi dengan Kartu akses parkiran motor dan CCTV 24.</p>
            </div>
        </div>
    </section>

    <section class="bg-white py-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-bold">Rekomendasi Kost</h2>
                    <p class="text-gray-500">Pilihan terbaik untuk kenyamanan maksimal.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php if (!empty($lk_featured)): ?>
                    <?php foreach ($lk_featured as $kost): ?>
                        <?php
                            $facs = json_decode($kost['facilities'] ?? '[]', true);
                            if (!is_array($facs)) { $facs = []; }
                            $facs = array_slice($facs, 0, 3);
                            $price = (isset($kost['price_from']) && $kost['price_from'] !== null)
                                ? 'Rp ' . number_format((float) $kost['price_from'], 0, ',', '.')
                                : 'Hubungi kami';
                            $img = !empty($kost['featured_image'])
                                ? '/images/' . htmlspecialchars($kost['featured_image'], ENT_QUOTES)
                                : 'https://placehold.co/800x600/f97316/ffffff?text=Living+Kost';
                        ?>
                        <a href="/detail?id=<?= (int) $kost['id'] ?>">
                        <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition h-full">
                            <div class="relative">
                                <img src="<?= $img ?>" alt="<?= htmlspecialchars($kost['name'], ENT_QUOTES) ?>" class="w-full h-64 object-cover">
                                <?php $avail = (int)($kost['available_rooms'] ?? 0); ?>
                                <span class="absolute top-4 left-4 <?= $avail > 0 ? 'bg-green-600' : 'bg-gray-500' ?> text-white text-xs font-bold px-3 py-1 rounded-full uppercase">
                                    <?= $avail > 0 ? 'Tersedia ' . $avail . ' Kamar' : 'Tidak Tersedia' ?>
                                </span>
                            </div>
                            <div class="p-6">
                                <?php if (!empty($kost['location_label'])): ?>
                                    <p class="text-orange-600 font-bold text-xs uppercase tracking-widest mb-1"><?= htmlspecialchars($kost['location_label'], ENT_QUOTES) ?></p>
                                <?php endif; ?>
                                <h4 class="text-xl font-bold mb-2 text-gray-900"><?= htmlspecialchars($kost['name'], ENT_QUOTES) ?></h4>
                                <?php
                                    $gt = $kost['gender_type'] ?? '';
                                    $gtLabel = match($gt) { 'putra' => 'Putra', 'putri' => 'Putri', 'putra_putri' => 'Putra & Putri', default => '' };
                                    $gtClass = match($gt) { 'putra' => 'bg-blue-100 text-blue-700', 'putri' => 'bg-pink-100 text-pink-700', 'putra_putri' => 'bg-purple-100 text-purple-700', default => '' };
                                ?>
                                <?php if ($gtLabel): ?>
                                    <span class="inline-block mb-3 px-2 py-0.5 rounded-full text-xs font-bold uppercase <?= $gtClass ?>"><?= htmlspecialchars($gtLabel, ENT_QUOTES) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($facs)): ?>
                                    <div class="flex flex-wrap items-center text-gray-500 text-sm mb-4 gap-x-4 gap-y-1">
                                        <?php foreach ($facs as $f): ?>
                                            <span><i class="fas <?= htmlspecialchars(lk_fa_icon($f), ENT_QUOTES) ?> text-orange-500 mr-1"></i> <?= htmlspecialchars($f, ENT_QUOTES) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="flex justify-between items-center border-t pt-4">
                                    <p class="text-gray-900 font-bold"><?= $price ?> <span class="text-gray-400 font-normal text-sm">/ bln</span></p>
                                </div>
                            </div>
                        </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="md:col-span-3 text-center text-gray-400 py-12">
                        Rekomendasi kost akan segera hadir.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="py-20 bg-orange-50 px-6 overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div class="max-w-xl">
                    <h2 class="text-3xl font-bold mb-4">Apa Kata Mereka?</h2>
                    <p class="text-gray-600">Lebih dari sekedar tinggal, kami menjadi bagian dari pengalaman penghuni</p>
                </div>
            </div>
    
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-md transition">
                    <div class="flex text-yellow-400 mb-4">
                        <i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i>
                    </div>
                    <p class="text-gray-700 mb-8 italic">"Jujurly, awalnya skeptis karena foto di web bagus banget, takut kena zonk. Pas survei, eh beneran cakep! Yang paling nolong sih WiFi-nya stabil pol, dipake meeting seharian nggak pernah rewel. Worth every penny sih buat kaum budak korporat kayak gue."</p>
                    <div class="flex items-center">
                        <!--<img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=150&q=80" class="w-12 h-12 rounded-full object-cover mr-4" alt="User">-->
                        <div>
                            <h4 class="font-bold text-gray-900">Reza, 26 tahun</h4>
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Karyawan Swasta</p>
                        </div>
                    </div>
                </div>
    
                <div class="bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-md transition">
                    <div class="flex text-yellow-400 mb-4">
                        <i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i>
                    </div>
                    <p class="text-gray-700 mb-8 italic">"Udah pindah-pindah kost 3 kali di Jakarta, baru di sini ngerasa beneran 'pulang'. Biasanya di kost lain mah pada ansos (anti sosial), tapi di sini anak-anaknya sering nongkrong bareng di mini bar. Penjaganya juga ramah banget, kalau ada paket pasti dijagain dengan aman. Berasa punya keluarga baru padahal jauh dari rumah."</p>
                    <div class="flex items-center">
                        <!--<img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=150&q=80" class="w-12 h-12 rounded-full object-cover mr-4" alt="User">-->
                        <div>
                            <h4 class="font-bold text-gray-900">Dimas Prayoga</h4>
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Mahasiswa</p>
                        </div>
                    </div>
                </div>
    
                <div class="bg-white p-8 rounded-[2rem] shadow-sm hover:shadow-md transition">
                    <div class="flex text-yellow-400 mb-4">
                        <i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i><i class="fas fa-star text-xs"></i>
                    </div>
                    <p class="text-gray-700 mb-8 italic">"Awalnya nyari kost yang nggak ribet soal parkiran sama akses 24 jam karena kerjaan gue sering lembur. Pas nemu Living Kost, vibes-nya langsung dapet banget. Di sini bener-bener tinggal rebahan atau kerja doang, urusan ribet lainnya udah ada yang beresin. Goks sih!"</p>
                    <div class="flex items-center">
                        <!--<img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=150&q=80" class="w-12 h-12 rounded-full object-cover mr-4" alt="User">-->
                        <div>
                            <h4 class="font-bold text-gray-900">Sarah Indira</h4>
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Karyawan BUMN</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white px-6">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4">Cara Mudah Booking di Living Kost</h2>
                <p class="text-gray-500">Proses cepat, transparan, dan sepenuhnya digital.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                <div class="text-center relative z-10">
                    <div class="w-16 h-16 bg-orange-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-lg shadow-orange-200">1</div>
                    <h3 class="font-bold text-lg mb-2">Pilih Unit</h3>
                    <p class="text-gray-500 text-sm">Cari lokasi kost yang paling strategis buat aktivitasmu.</p>
                </div>
                <div class="text-center relative z-10">
                    <div class="w-16 h-16 bg-orange-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-lg shadow-orange-200">2</div>
                    <h3 class="font-bold text-lg mb-2">Survei Online/Offline</h3>
                    <p class="text-gray-500 text-sm">Lihat dengan video call atau jadwalkan kunjungan langsung.</p>
                </div>
                <div class="text-center relative z-10">
                    <div class="w-16 h-16 bg-orange-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-lg shadow-orange-200">3</div>
                    <h3 class="font-bold text-lg mb-2">Bayar Aman</h3>
                    <p class="text-gray-500 text-sm">Jika sudah cocok, kamu bisa melakukan DP atau bayar langsung didepan.</p>
                </div>
                <div class="text-center relative z-10">
                    <div class="w-16 h-16 bg-orange-600 text-white rounded-2xl flex items-center justify-center text-2xl font-bold mx-auto mb-6 shadow-lg shadow-orange-200">4</div>
                    <h3 class="font-bold text-lg mb-2">Langsung Pindah</h3>
                    <p class="text-gray-500 text-sm">Tinggal bawa koper, dan komunikasikan kedatangan dengan penjaga kami.</p>
                </div>
                
                <div class="hidden md:block absolute top-8 left-0 w-full h-0.5 bg-gray-100 -z-0"></div>
            </div>
        </div>
    </section>
    
    <section class="py-20 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="bg-slate-900 rounded-[3rem] overflow-hidden relative shadow-2xl">
                <div class="absolute top-0 right-0 w-64 h-64 bg-orange-600/20 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-600/10 rounded-full -ml-32 -mb-32 blur-3xl"></div>
    
                <div class="relative z-10 flex flex-col md:flex-row items-center p-10 md:p-20">
                    <div class="flex-1 text-center md:text-left mb-10 md:mb-0">
                        <span class="text-orange-500 font-bold uppercase tracking-widest text-sm mb-4 block">Kamar kami cepat habis</span>
                        <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6 leading-tight">
                            Dapatkan Kamarmu <br class="hidden md:block"> Sebelum Kehabisan!
                        </h2>
                        <p class="text-slate-400 text-lg mb-8 max-w-lg">
                            Tanya ketersediaan unit dan promo yang tersedia.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                            <a href="https://wa.me/6285161180441?text=Halo%20Admin%2C%20boleh%20informasinya%20mengenai%20kosan%20yang%20tersedia%20dan%20promonya%3F" class="inline-flex items-center justify-center bg-green-500 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-green-600 transition shadow-lg shadow-green-900/20">
                                <i class="fab fa-whatsapp text-2xl mr-3"></i>
                                Cek Ketersediaan
                            </a>
                        </div>
                    </div>
    
                    <div class="flex-1 flex justify-center md:justify-end">
                        <div class="bg-white/5 backdrop-blur-md border border-white/10 p-8 rounded-3xl text-center w-full max-w-xs">
                            <div class="w-20 h-20 bg-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-xl">
                                <i class="fas fa-headset text-white text-3xl"></i>
                            </div>
                            <h4 class="text-white font-bold text-xl mb-1">Respon Cepat</h4>
                            <p class="text-slate-400 text-sm">Tim kami selalu siap membantu.</p>
                            <div class="mt-6 pt-6 border-t border-white/10 flex justify-center space-x-4">
                                <div class="text-center">
                                    <p class="text-white font-bold">100+</p>
                                    <p class="text-slate-500 text-[10px] uppercase">Penghuni</p>
                                </div>
                                <div class="w-px h-8 bg-white/10"></div>
                                <div class="text-center">
                                    <p class="text-white font-bold">4.9/5</p>
                                    <p class="text-slate-500 text-[10px] uppercase">Rating</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-12 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <div class="text-2xl font-bold text-orange-500 mb-4">Living Kost</div>
                <p class="text-gray-400 max-w-sm">Solusi hunian modern untuk anak muda di Indonesia yang mencari kenyamanan dan koneksi.</p>
            </div>
            <div>
                <h5 class="font-bold mb-4">Pusat Bantuan</h5>
                <ul class="text-gray-400 space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white">Syarat & Ketentuan</a></li>
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

</body>
</html>