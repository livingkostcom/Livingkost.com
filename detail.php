<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Living Kost Menteng | Detail Kamar</title>
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
            <a href="#" class="hover:text-orange-600">Home</a> / 
            <a href="#" class="hover:text-orange-600">Jakarta Selatan</a> / 
            <span class="text-gray-900 font-semibold">Living Kost Pejaten</span>
        </nav>

        <!-- start image -->
        <div class="max-w-7xl mx-auto mb-10 px-4 md:px-0">
            <div class="flex flex-col lg:flex-row gap-4">
                
                <div class="flex-1">
                    <div class="relative h-[300px] md:h-[550px] overflow-hidden rounded-2xl shadow-sm bg-gray-100">
                        <img id="mainView" src="/images/Dapur.png" 
                             class="w-full h-full object-cover cursor-zoom-in transition-all duration-500"
                             onclick="openGallery(this.src)"
                             alt="Dapur">
                        
                        <div class="absolute bottom-4 right-4 bg-black/60 backdrop-blur-md text-white px-3 py-1 rounded-full text-xs">
                            <span id="currentImgIdx">1</span> / 6
                        </div>
                    </div>
                </div>
        
                <div class="flex lg:flex-col overflow-x-auto lg:overflow-y-auto gap-3 no-scrollbar lg:w-32 lg:h-[550px] py-1">
                    
                    <div class="min-w-[100px] lg:min-w-full h-24 cursor-pointer rounded-xl overflow-hidden border-2 border-orange-600 thumb-item opacity-100 shrink-0 transition"
                         onclick="changeImage(this, '/images/Dapur.png')">
                        <img src="/images/Dapur.png" class="w-full h-full object-cover">
                    </div>
        
                    <div class="min-w-[100px] lg:min-w-full h-24 cursor-pointer rounded-xl overflow-hidden border-2 border-transparent thumb-item opacity-60 hover:opacity-100 shrink-0 transition"
                         onclick="changeImage(this, '/images/Dapur2.png')">
                        <img src="/images/Dapur2.png" class="w-full h-full object-cover">
                    </div>
        
                    <div class="min-w-[100px] lg:min-w-full h-24 cursor-pointer rounded-xl overflow-hidden border-2 border-transparent thumb-item opacity-60 hover:opacity-100 shrink-0 transition"
                         onclick="changeImage(this, '/images/R.Makan.png')">
                        <img src="/images/R.Makan.png" class="w-full h-full object-cover">
                    </div>
        
                    <div class="min-w-[100px] lg:min-w-full h-24 cursor-pointer rounded-xl overflow-hidden border-2 border-transparent thumb-item opacity-60 hover:opacity-100 shrink-0 transition"
                         onclick="changeImage(this, '/images/Meja Makan.png')">
                        <img src="/images/Meja Makan.png" class="w-full h-full object-cover">
                    </div>
        
                    <div class="min-w-[100px] lg:min-w-full h-24 cursor-pointer rounded-xl overflow-hidden border-2 border-transparent thumb-item opacity-60 hover:opacity-100 shrink-0 transition"
                         onclick="changeImage(this, '/images/Jemuran.jpeg')">
                        <img src="/images/Jemuran.jpeg" class="w-full h-full object-cover">
                    </div>
                    
                    <div class="min-w-[100px] lg:min-w-full h-24 cursor-pointer rounded-xl overflow-hidden border-2 border-transparent thumb-item opacity-60 hover:opacity-100 shrink-0 transition"
                         onclick="changeImage(this, '/images/Kamar4.png')">
                        <img src="/images/Kamar4.png" class="w-full h-full object-cover">
                    </div>
                    
                     <div class="min-w-[100px] lg:min-w-full h-24 cursor-pointer rounded-xl overflow-hidden border-2 border-transparent thumb-item opacity-60 hover:opacity-100 shrink-0 transition"
                         onclick="changeImage(this, '/images/Kamar5.jpeg')">
                        <img src="/images/Kamar5.jpeg" class="w-full h-full object-cover">
                    </div>
                    
                    <div class="min-w-[100px] lg:min-w-full h-24 cursor-pointer rounded-xl overflow-hidden border-2 border-transparent thumb-item opacity-60 hover:opacity-100 shrink-0 transition"
                         onclick="changeImage(this, '/images/Kamar Mandi.png')">
                        <img src="/images/Kamar Mandi.png" class="w-full h-full object-cover">
                    </div>
        
                    <div class="min-w-[100px] lg:min-w-full h-24 cursor-pointer rounded-xl overflow-hidden border-2 border-transparent thumb-item opacity-60 hover:opacity-100 shrink-0 transition"
                         onclick="changeImage(this, '/images/Garasi.jpeg')">
                        <img src="/images/Garasi.jpeg" class="w-full h-full object-cover">
                    </div>
                    
                    <div class="min-w-[100px] lg:min-w-full h-24 cursor-pointer rounded-xl overflow-hidden border-2 border-transparent thumb-item opacity-60 hover:opacity-100 shrink-0 transition"
                         onclick="changeImage(this, '/images/Luar3.jpeg')">
                        <img src="/images/Luar3.jpeg" class="w-full h-full object-cover">
                    </div>
                    
                    <div class="min-w-[100px] lg:min-w-full h-24 cursor-pointer rounded-xl overflow-hidden border-2 border-transparent thumb-item opacity-60 hover:opacity-100 shrink-0 transition"
                         onclick="changeImage(this, '/images/Luar1.jpeg')">
                        <img src="/images/Luar1.jpeg" class="w-full h-full object-cover">
                    </div>
        
                </div>
            </div>
        </div>
        
        <style>
            /* Sembunyikan scrollbar di semua browser */
            .no-scrollbar::-webkit-scrollbar { display: none; }
            .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
            
            /* Efek transisi halus untuk gambar utama */
            #mainView { object-position: center; }
        </style>
        <!-- end image -->
        
        <div id="galleryModal" class="fixed inset-0 z-[100] hidden bg-black/90 flex items-center justify-center p-4 backdrop-blur-sm">
            <button onclick="closeGallery()" class="absolute top-6 right-6 text-white text-4xl hover:text-orange-500 transition">&times;</button>
            <img id="modalImage" src="" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl">
        </div>

        <div class="flex flex-col md:flex-row gap-10">
            <div class="flex-1">
                <div class="border-b pb-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Living Kost Pejaten</h1>
                    <p class="text-gray-500 mb-4"><i class="fas fa-location-dot text-orange-600 mr-2"></i>Jl. Bank Niaga No.47B 15, RT.15/RW.3, Pejaten Bar., Ps. Minggu, Jakarta</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">Putra & Putri</span>
                        <span class="bg-green-50 text-green-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">Tersedia 2 Kamar</span>
                    </div>
                </div>

                <div class="py-10 border-b">
                    <h2 class="text-xl font-bold mb-6">Fasilitas Umum</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-sink w-8 text-orange-500"></i>
                            <span>Dapur Gratis Gas</span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-clipboard w-8 text-orange-500"></i>
                            <span>Kulkas</span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-droplet w-8 text-orange-500"></i>
                            <span>Dispenser Air Minum</span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-soap w-8 text-orange-500"></i>
                            <span>Mesin Cuci</span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-sun w-8 text-orange-500"></i>
                            <span>Tempat Jemur</span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-utensils w-8 text-orange-500"></i>
                            <span>Meja Makan Sky View</span>
                        </div>
                    </div>
                </div>
                
                <div class="py-10 border-b">
                    <h2 class="text-xl font-bold mb-6">Fasilitas Kamar</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-snowflake w-8 text-orange-500"></i>
                            <span>AC</span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-bed w-8 text-orange-500"></i>
                            <span>Kasur</span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-wifi w-8 text-orange-500"></i>
                            <span>WiFi 100 Mbps</span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-briefcase w-8 text-orange-500"></i>
                            <span>Meja Kerja</span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-shower w-8 text-orange-500"></i>
                            <span>Kamar Mandi Dalam</span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-toilet w-8 text-orange-500"></i>
                            <span>Kloset Duduk</span>
                        </div>
                    </div>
                </div>

                <div class="py-10 border-b">
                    <h2 class="text-xl font-bold mb-4">Pilihan Tipe Kamar</h2>
                    <p class="text-gray-600 leading-relaxed mb-4">
                        Living Kost Pejaten Barat dekat ST Pasar Minggu, Pancoran, Kalibata. Kost Eksklusif murah bagi Karyawan, Karyawati, Mahasiswa dan Profesional Muda.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        Kami memiliki 3 jenis kamar dengan berbagai fasilitas umum GRATIS yang bisa menghemat dan membuat nyaman seluruh penghuni seperti:
                    </p>
                    <!-- Kamar -->
                    <section class="py-6 px-6 bg-transparent">
                        <div class="max-w-7xl mx-auto">
                            <div class="space-y-8">
                                
                                <div class="flex flex-col md:flex-row gap-6 items-start border-b border-gray-100 pb-8">
                                    <div class="relative w-full md:w-1/4 group shrink-0">
                                        <div class="flex overflow-x-auto snap-x snap-mandatory no-scrollbar gap-2 rounded-xl h-44 md:h-48">
                                            <img src="https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=600&q=80" 
                                                 class="min-w-full h-full object-cover snap-center cursor-pointer hover:opacity-90 transition" onclick="openGallery(this.src)">
                                            <img src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=600&q=80" 
                                                 class="min-w-full h-full object-cover snap-center cursor-pointer hover:opacity-90 transition" onclick="openGallery(this.src)">
                                        </div>
                                        <div class="absolute bottom-3 right-3 bg-black/50 backdrop-blur-sm text-white text-[10px] px-2 py-1 rounded-lg pointer-events-none">
                                            <i class="fas fa-image mr-1"></i> 1/2
                                        </div>
                                    </div>
                    
                                    <div class="flex-1 pt-1">
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">Superior</h3>
                                        <p class="text-gray-500 text-sm leading-relaxed max-w-xl mb-3">
                                            Berada di lantai 2 dengan ukuran 3m X 3m. Kamar ini menggunakan konsep Tatami Bed dengan kasur 100x200. Sangat cocok untuk anda yang masih single.
                                        </p>
                                        <div class="flex gap-6">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold text-gray-400 uppercase">Harga</span>
                                                <span class="text-orange-600 text-sm font-bold">Rp 1.800.000<span class="text-gray-400 font-normal">/bln</span></span>
                                                <span class="text-gray-400 text-sm font-bold line-through italic">Rp 2.000.000</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    
                                <div class="flex flex-col md:flex-row gap-6 items-start border-b border-gray-100 pb-8">
                                    <div class="relative w-full md:w-1/4 group shrink-0">
                                        <div class="flex overflow-x-auto snap-x snap-mandatory no-scrollbar gap-2 rounded-xl h-44 md:h-48">
                                            <img src="/images/Kamar4.png" 
                                                 class="min-w-full h-full object-cover snap-center cursor-pointer hover:opacity-90 transition" onclick="openGallery(this.src)">
                                            <img src="/images/Kamar Mandi.png" 
                                                 class="min-w-full h-full object-cover snap-center cursor-pointer hover:opacity-90 transition" onclick="openGallery(this.src)">
                                        </div>
                                        <div class="absolute bottom-3 right-3 bg-black/50 backdrop-blur-sm text-white text-[10px] px-2 py-1 rounded-lg pointer-events-none">
                                            <i class="fas fa-image mr-1"></i> 1/2
                                        </div>
                                    </div>
                    
                                    <div class="flex-1 pt-1">
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">Deluxe</h3>
                                        <p class="text-gray-500 text-sm leading-relaxed max-w-xl mb-3">
                                            Berada di lantai 1 dengan ukuran 2.5 m X 3.5m. Fasilitas TV dengan kasur 100cm X 200cm. Cocok bagi mobilitas tinggi.
                                        </p>
                                        <div class="flex gap-6">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold text-gray-400 uppercase">Harga</span>
                                                <span class="text-orange-600 text-sm font-bold">Rp 2.000.000<span class="text-gray-400 font-normal">/bln</span></span>
                                                <span class="text-gray-400 text-sm font-bold line-through italic">Rp 2.300.000</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    
                                <div class="flex flex-col md:flex-row gap-6 items-start pb-4">
                                    <div class="relative w-full md:w-1/4 group shrink-0">
                                        <div class="flex overflow-x-auto snap-x snap-mandatory no-scrollbar gap-2 rounded-xl h-44 md:h-48">
                                            <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=600&q=80" 
                                                 class="min-w-full h-full object-cover snap-center cursor-pointer hover:opacity-90 transition" onclick="openGallery(this.src)">
                                            <img src="https://images.unsplash.com/photo-1616137422495-1e9e46e2aa77?auto=format&fit=crop&w=600&q=80" 
                                                 class="min-w-full h-full object-cover snap-center cursor-pointer hover:opacity-90 transition" onclick="openGallery(this.src)">
                                        </div>
                                        <div class="absolute bottom-3 right-3 bg-black/50 backdrop-blur-sm text-white text-[10px] px-2 py-1 rounded-lg pointer-events-none">
                                            <i class="fas fa-image mr-1"></i> 1/2
                                        </div>
                                    </div>
                    
                                    <div class="flex-1 pt-1">
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">Suite</h3>
                                        <p class="text-gray-500 text-sm leading-relaxed max-w-xl mb-3">
                                            Lantai 3 & 4. Ukuran 3.5m X 4m. Smart TV dan 2 kasur (100x200). Sangat cocok untuk tinggal berdua.
                                        </p>
                                        <div class="flex gap-6">
                                            <div class="flex items-center gap-2">
                                                <span class="text-[10px] font-bold text-gray-400 uppercase">Harga</span>
                                                <span class="text-orange-600 text-sm font-bold">Rp 2.200.000<span class="text-gray-400 font-normal">/bln</span></span>
                                                <span class="text-gray-400 text-sm font-bold line-through italic">Rp 2.500.000</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    
                            </div>
                        </div>
                    </section>
                    
                    <div id="galleryModal" class="fixed inset-0 z-[100] hidden bg-black/95 flex items-center justify-center p-4 backdrop-blur-md">
                        <button onclick="closeGallery()" class="absolute top-6 right-6 text-white text-4xl hover:text-orange-500 transition">&times;</button>
                        <img id="modalImage" src="" class="max-w-full max-h-[90vh] rounded-xl shadow-2xl border border-white/10">
                    </div>
                    <!-- kamar End -->
                </div>
                
                <div class="py-10 border-b">
                    <h2 class="text-xl font-bold mb-4">Lokasi Kost</h2>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.9450753270744!2d106.8401179!3d-6.2709535999999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3c30af693f7%3A0x2450664bfb72a94d!2sLiving%20Kost%20Pejaten!5e0!3m2!1sid!2sid!4v1775745790658!5m2!1sid!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

            <div class="w-full md:w-80 lg:w-96">
                <div class="sticky sticky-card bg-white border border-gray-100 rounded-3xl shadow-2xl p-8">
                    <p class="text-sm text-gray-500 mb-1">Harga Promo</p>
                    <div class="flex items-end space-x-2 mb-6">
                        <span id="displayPrice" class="text-3xl font-bold text-gray-900">Rp 1.800.000</span>
                        <span class="text-gray-500 pb-1">/ bulan</span>
                    </div>
            
                    <div class="space-y-4 mb-6">
                        <div class="p-3 border rounded-xl bg-gray-50">
                            <label class="block text-[10px] font-bold uppercase text-gray-400">Tipe Kamar</label>
                            <select id="roomSelect" class="w-full bg-transparent outline-none text-sm font-semibold cursor-pointer">
                                <option value="Superior">Superior</option>
                                <option value="Deluxe">Deluxe</option>
                                <option value="Suite">Suite</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a id="waButton" href="#" target="_blank" class="inline-flex items-center justify-center bg-green-500 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-green-600 transition shadow-lg shadow-green-900/20 w-full md:w-auto">
                            <i class="fab fa-whatsapp text-2xl mr-3"></i>
                            Tanya Ketersediaan
                        </a>
                    </div>
            
                    <div class="mt-6 pt-6 border-t text-center">
                        <p class="text-xs text-gray-400 font-medium"><i class="fas fa-shield-check mr-2"></i>Pembayaran Aman & Terverifikasi</p>
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

    <script>
        function changeImage(element, src) {
            const mainImg = document.getElementById('mainView');
            
            // Animasi fade out sederhana
            mainImg.style.opacity = '0.5';
            
            setTimeout(() => {
                mainImg.src = src;
                mainImg.style.opacity = '1';
            }, 150);
    
            // Update border thumbnail
            const thumbs = document.querySelectorAll('.thumb-item');
            thumbs.forEach(thumb => {
                thumb.classList.remove('border-orange-600', 'opacity-100');
                thumb.classList.add('border-transparent', 'opacity-60');
            });
    
            element.classList.remove('border-transparent', 'opacity-60');
            element.classList.add('border-orange-600', 'opacity-100');
    
            // Fitur SMART SCROLL: 
            // Membuat thumbnail yang diklik otomatis bergeser ke area pandang
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest',
                inline: 'center'
            });
        }
    </script>
    <script>
        const roomSelect = document.getElementById('roomSelect');
        const displayPrice = document.getElementById('displayPrice');
        const waButton = document.getElementById('waButton');
        const phoneNumber = "6285161180441";
    
        // Data Harga Kamar (Pastikan formatnya sesuai keinginan Anda)
        const roomData = {
            "Superior": "Rp 1.800.000",
            "Deluxe": "Rp 2.000.000",
            "Suite": "Rp 2.200.000"
        };
    
        function updateContent() {
            const selectedRoom = roomSelect.value;
            const selectedPrice = roomData[selectedRoom]; // Mengambil harga berdasarkan tipe
            
            // 1. Update Tampilan Harga di UI
            displayPrice.innerText = selectedPrice;
    
            // 2. Update Link WhatsApp dengan Format Pesan Baru
            // Menggunakan template literal agar harga otomatis masuk ke dalam pesan
            const message = `Halo admin, saya mau tanya apakah promo kamar ${selectedRoom} harga ${selectedPrice} nya masih tersedia?`;
            
            const waUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
            waButton.href = waUrl;
        }
    
        // Jalankan fungsi saat dropdown berubah
        roomSelect.addEventListener('change', updateContent);
    
        // Jalankan saat halaman pertama kali dimuat
        updateContent();
    </script>
    <script>
        function openGallery(src) {
            const modal = document.getElementById('galleryModal');
            const modalImg = document.getElementById('modalImage');
            
            modalImg.src = src;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Stop scrolling
        }
    
        function closeGallery() {
            const modal = document.getElementById('galleryModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto'; // Re-enable scrolling
        }
    
        // Tutup modal jika klik di luar gambar
        document.getElementById('galleryModal').addEventListener('click', function(e) {
            if (e.target === this) closeGallery();
        });
    </script>
</body>
</html>