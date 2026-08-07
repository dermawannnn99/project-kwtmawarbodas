<?php
// Security & error reporting headers dikirim sebelum output apapun
require_once 'config/database.php';

// Simple request logger to help debug mobile/iOS issues (writes to /logs/requests.log)
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
$logFile = $logDir . '/requests.log';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$actionLog = $_GET['action'] ?? '';
file_put_contents($logFile, date('c') . " | IP={$ip} | HOST={$_SERVER['HTTP_HOST']} | ACTION={$actionLog} | UA={$ua}" . PHP_EOL, FILE_APPEND);

// [SEC-7] Validasi format batch_code sebelum dipakai — hanya alfanumerik dan dash
$raw_scan = $_GET['scan'] ?? '';
$auto_scan_batch = preg_match('/^[A-Za-z0-9\-]+$/', $raw_scan) ? $raw_scan : '';

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
// [SEC-6] CSP — izinkan CDN yang dipakai
header("Content-Security-Policy: default-src 'self'; " .
    "script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com; " .
    "style-src 'self' 'unsafe-inline' https://fonts.cdnfonts.com https://fonts.googleapis.com https://unpkg.com https://cdn.jsdelivr.net; " .
    "font-src 'self' https://fonts.cdnfonts.com https://fonts.gstatic.com https://unpkg.com https://cdn.jsdelivr.net; " .
    "img-src 'self' data: https: blob:; " .
    "connect-src 'self' https://api.qrserver.com;");

// =========================================================================
// LOGIKA API BACKEND (Menangani AJAX Request dari Frontend JavaScript)
// =========================================================================
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    if (isset($db_error)) {
        error_log("[index.php] DB error: $db_error");
        echo json_encode(['status' => 'error', 'message' => 'Koneksi database bermasalah.']);
        exit;
    }

    $action = $_GET['action'];

    // 1. GET ALL PRODUCTS — untuk halaman publik: hanya yang is_visible=1
    if ($action === 'get_products') {
        $stmt = $pdo->query("SELECT * FROM products WHERE is_visible = 1 ORDER BY id DESC");
        $products = $stmt->fetchAll();
        echo json_encode(['status' => 'success', 'data' => $products]);
        exit;
    }

    // 2. SAVE PRODUCT (CREATE ATAU UPDATE)
    // [DIHAPUS] Handler ini dipindah ke pages/admin-api.php yang memiliki session guard.
    // Akses langsung ke index.php?action=save_product tidak lagi diizinkan.

    // 3. DELETE PRODUCT
    // [DIHAPUS] Handler ini dipindah ke pages/admin-api.php yang memiliki session guard.
    // Akses langsung ke index.php?action=delete_product tidak lagi diizinkan.

    // 4. SCAN BATCH (Berdasarkan parameter scan QR Code)
    if ($action === 'scan_batch') {
        // [SEC-7] Validasi format batch_code sebelum query
        $batch = $_GET['batch_code'] ?? '';
        if (!preg_match('/^[A-Za-z0-9\-]+$/', $batch)) {
            echo json_encode(['status' => 'error', 'message' => 'Format kode batch tidak valid.']);
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM products WHERE batch_code=?");
        $stmt->execute([$batch]);
        $product = $stmt->fetch();
        if ($product) {
            echo json_encode(['status' => 'success', 'data' => $product]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Kode Batch tidak valid atau palsu!']);
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Kelompok Wanita Tani (KWT) Mawar Bodas II</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.cdnfonts.com/css/satoshi" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@1&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- MENGGUNAKAN VERSI STABIL 2.3.8 UNTUK MENCEGAH CRASH PADA BROWSER DESKTOP -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js" type="text/javascript"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Satoshi', 'sans-serif'] },
                    colors: {
                        brand: { light: '#E4F0EE', DEFAULT: '#1E6472', dark: '#123F48', accent: '#D4A017' }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- [QUAL-1] Shared utilities (escHtml, formatRupiah) -->
    <script src="assets/js/shared-utils.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased selection:bg-brand selection:text-white">

    <!-- Navigasi Utama -->
    <nav class="bg-white/80 backdrop-blur-md shadow-sm fixed w-full z-50 top-0 border-b border-gray-100">
        <div class="site-container">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-lg md:text-2xl font-black text-brand flex items-center gap-2 tracking-tight">
                        <img src="assets/img/logo-kwt.png" alt="Logo KWT Mawar Bodas II" class="w-8 h-8 object-contain"> KWT Mawar Bodas II
                    </span>
                </div>
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="profil.php" class="text-gray-600 hover:text-brand font-medium transition-colors">Profil</a>
                    <a href="#keunggulan" class="text-gray-600 hover:text-brand font-medium transition-colors">Keunggulan</a>
                    <a href="#produk" class="text-gray-600 hover:text-brand font-medium transition-colors">Katalog Produk</a>
                    <a href="#qrcode" class="text-gray-600 hover:text-brand font-medium transition-colors">Validasi QR Kemasan</a>
                    <a href="pages/login.php" class="bg-brand/10 hover:bg-brand text-brand hover:text-white px-4 py-2 rounded-xl font-bold transition-all">
                        Login
                    </a>
                </div>
                <div class="md:hidden flex items-center gap-3">
                    <a href="pages/login.php" class="text-brand font-bold text-sm bg-brand/10 px-3 py-1.5 rounded-lg">Login</a>
                    <button onclick="openMobileMenu()" aria-label="Buka menu navigasi"
                        class="text-gray-700 hover:text-brand transition-colors p-1">
                        <i class="ph-bold ph-list text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Drawer Menu -->
    <div id="mobile-menu-overlay"
         onclick="closeMobileMenu()"
         class="fixed inset-0 bg-black/50 z-[60] hidden opacity-0 transition-opacity duration-300"></div>

    <div id="mobile-menu-panel"
         class="fixed top-0 right-0 h-full w-72 max-w-[80%] bg-white z-[70] shadow-2xl translate-x-full transition-transform duration-300 ease-in-out flex flex-col">

        <!-- Header drawer -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <span class="font-black text-brand text-base tracking-tight">Menu</span>
            <button onclick="closeMobileMenu()" aria-label="Tutup menu"
                class="text-gray-500 hover:text-brand transition-colors p-1">
                <i class="ph-bold ph-x text-xl"></i>
            </button>
        </div>

        <!-- Nav items -->
        <nav class="flex-1 overflow-y-auto py-2">
            <a href="profil.php" onclick="closeMobileMenu()"
               class="flex items-center gap-3 px-5 py-3 text-gray-700 font-medium hover:bg-brand-light hover:text-brand transition-colors border-b border-gray-50">
                <i class="ph-bold ph-user-circle text-lg text-brand/70"></i> Profil
            </a>
            <a href="#keunggulan" onclick="closeMobileMenu()"
               class="flex items-center gap-3 px-5 py-3 text-gray-700 font-medium hover:bg-brand-light hover:text-brand transition-colors border-b border-gray-50">
                <i class="ph-bold ph-star text-lg text-brand/70"></i> Keunggulan
            </a>
            <a href="#produk" onclick="closeMobileMenu()"
               class="flex items-center gap-3 px-5 py-3 text-gray-700 font-medium hover:bg-brand-light hover:text-brand transition-colors border-b border-gray-50">
                <i class="ph-bold ph-storefront text-lg text-brand/70"></i> Katalog Produk
            </a>
            <a href="#qrcode" onclick="closeMobileMenu()"
               class="flex items-center gap-3 px-5 py-3 text-gray-700 font-medium hover:bg-brand-light hover:text-brand transition-colors border-b border-gray-50">
                <i class="ph-bold ph-qr-code text-lg text-brand/70"></i> Validasi QR Kemasan
            </a>
        </nav>
    </div>

    <!-- Hero Section -->
    <div class="relative pt-24 pb-16 md:pt-32 md:pb-24 overflow-hidden bg-gradient-to-br from-brand-light/50 via-white to-gray-50">
        <!-- Wallpaper tekstur halus, opacity sangat rendah -->
        <div class="absolute inset-0 z-0 pointer-events-none" style="background-image:url('assets/img/wallpaper.png');background-size:cover;background-position:center;opacity:0.065;"></div>
        <div class="absolute inset-0 z-0 opacity-40">
            <div class="absolute top-0 right-0 w-96 h-96 bg-brand-light rounded-full blur-3xl"></div>
        </div>
        <div class="site-container relative z-10 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="hero-enter">
                <h1 class="text-5xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6 text-gray-900 tracking-tight">
                    Olahan Premium dalam <em class="hero-em-curly text-brand">Kemasan Pintar</em>
                </h1>
                <p class="text-base md:text-lg text-gray-600 mb-8 max-w-lg leading-relaxed">
                    Nikmati sajian tradisional instan premium higienis. Dilengkapi teknologi QR Code Dinamis terintegrasi database untuk melacak verifikasi keaslian dan batch produksi real-time.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#produk" class="bg-brand hover:bg-brand-dark text-white px-8 py-4 rounded-xl font-bold text-center shadow-lg shadow-brand/20 transition-all transform hover:-translate-y-0.5">Lihat Katalog Menu</a>
                    <a href="#qrcode" class="bg-white border-2 border-brand text-brand hover:bg-brand-light px-8 py-4 rounded-xl font-bold text-center transition-all">Pindai Kode Kemasan</a>
                </div>
            </div>
            <div class="hero-enter-right relative flex justify-center">
                <img src="assets/img/gambarhero.png" class="w-full max-w-xl h-auto object-contain" alt="Produk KWT Mawar Bodas II">
            </div>
        </div>
    </div>

    <!-- Section Tentang Kami -->
    <section class="reveal-right py-10 md:py-14 bg-gray-50">
        <div class="site-container">
            <!-- Card dengan foto background -->
            <div class="relative overflow-hidden rounded-3xl shadow-lg min-h-[360px] md:min-h-[380px] flex items-center">

                <!-- Background foto kegiatan KWT -->
                <img src="assets/img/foto-kwt-kegiatan.jpg"
                     class="absolute inset-0 w-full h-full object-cover"
                     alt="Kegiatan KWT Mawar Bodas II">

                <!-- Overlay gradient brand: pekat di kiri, transparan di kanan -->
                <div class="absolute inset-0 bg-gradient-to-r from-brand-dark/95 via-brand-dark/80 to-brand-dark/30"></div>

                <!-- Konten di atas overlay -->
                <div class="relative z-10 flex flex-col justify-center p-6 md:p-14 max-w-xl">

                    <!-- Label kecil -->
                    <p class="text-xs font-bold uppercase tracking-widest text-white/60 mb-2 md:mb-3">Tentang Kami</p>

                    <!-- Judul -->
                    <h2 class="text-2xl md:text-4xl font-extrabold text-white tracking-tight mb-3 md:mb-4 leading-tight">
                        Mengenal KWT Mawar Bodas II
                    </h2>

                    <!-- Deskripsi -->
                    <p class="text-white/90 text-sm md:text-base leading-relaxed max-w-lg mb-4 md:mb-6">
                        Berawal dari bagian Kelompok Tani Mawar Bodas 2, sekelompok ibu rumah tangga di Desa Bengle membentuk kelompok khusus wanita tani pada tahun 2019. Kini KWT Mawar Bodas II beranggotakan 21 orang yang aktif dalam bidang pertanian dan pembuatan olahan pangan rumahan.
                    </p>

                    <!-- Badge angka -->
                    <div class="flex flex-wrap gap-5 md:gap-10 mb-5 md:mb-8">
                        <div>
                            <p class="text-xl md:text-3xl font-black text-white leading-none">2019</p>
                            <p class="text-xs text-white/70 uppercase tracking-wide mt-1">Tahun Berdiri</p>
                        </div>
                        <div>
                            <p class="text-xl md:text-3xl font-black text-white leading-none">21</p>
                            <p class="text-xs text-white/70 uppercase tracking-wide mt-1">Anggota Aktif</p>
                        </div>
                        <div>
                            <p class="text-xl md:text-3xl font-black text-white leading-none">Desa Bengle</p>
                            <p class="text-xs text-white/70 uppercase tracking-wide mt-1">Lokasi</p>
                        </div>
                    </div>

                    <!-- Tombol CTA -->
                    <a href="profil.php"
                       class="inline-flex items-center gap-2 bg-white text-brand-dark font-bold px-6 py-3 rounded-xl hover:bg-brand-light transition-all w-fit">
                        Lihat Profil Lengkap
                        <i class="ph-bold ph-arrow-right"></i>
                    </a>

                </div>
            </div>
        </div>
    </section>

    <!-- Seksi Web-Camera Scanner -->
    <section id="qrcode" class="reveal-left py-16 md:py-24 bg-gray-950 text-white relative">
        <div class="site-container relative z-10">
            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-3xl p-8 md:p-12">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 bg-brand/20 text-brand-light px-4 py-1.5 rounded-full font-semibold text-sm mb-6">
                            <i class="ph-bold ph-shield-check"></i> Teknologi Transparansi Rantai Pasok
                        </div>
                        <h2 class="text-2xl md:text-4xl font-extrabold mb-6 tracking-tight">Verifikasi Keaslian Produk</h2>
                        <p class="text-gray-300 mb-8 text-base leading-relaxed">
                            Pastikan Anda mengonsumsi produk orisinal dari produsen resmi. Klik tombol di bawah ini untuk mengizinkan akses kamera browser Anda dan pindai QR Code unik di stiker kemasan.
                        </p>
                        <button onclick="startScanner()" class="bg-brand hover:bg-brand-dark text-white px-8 py-4 rounded-xl font-bold transition-all flex items-center justify-center gap-2 w-full sm:w-auto shadow-lg shadow-brand/10">
                            <i class="ph-bold ph-camera text-xl"></i> Aktifkan Kamera Pemindai
                        </button>
                    </div>
                    
                    <div class="flex flex-col justify-center bg-white p-6 rounded-2xl relative min-h-[350px] shadow-2xl">
                        <!-- Area Kontainer Scanner -->
                        <div id="qr-reader" class="w-full max-w-sm hidden mx-auto"></div>

                        <!-- Tombol Kontrol Kamera (switch + flash) — muncul hanya saat kamera aktif -->
                        <div id="scanner-controls" class="hidden flex justify-center gap-3 mt-3">
                            <button onclick="switchCamera()" id="btn-switch-cam" title="Ganti Kamera"
                                class="hidden items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-4 py-2 rounded-xl text-sm transition-colors">
                                <i class="ph-bold ph-camera-rotate text-lg"></i> Ganti Kamera
                            </button>
                            <button onclick="toggleFlash()" id="btn-flash" title="Toggle Flash"
                                class="hidden items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-4 py-2 rounded-xl text-sm transition-colors">
                                <i id="flash-icon" class="ph-bold ph-lightning text-lg"></i>
                                <span id="flash-label">Flash</span>
                            </button>
                        </div>
                        
                        <!-- Placeholder Saat Kamera Belum Aktif -->
                        <div id="scanner-placeholder" class="text-center py-12 text-gray-800">
                            <div class="bg-brand-light w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="ph-fill ph-qr-code text-4xl text-brand"></i>
                            </div>
                            <p class="text-gray-900 font-bold text-lg">Pratinjau Scanner Kamera</p>
                            <p class="text-gray-400 text-sm max-w-xs mx-auto mt-2">Izinkan hak akses kamera pada dialog browser saat diminta untuk memulai peninjauan</p>
                        </div>
                        
                        <!-- Tombol Penutup Manual -->
                        <button onclick="stopScanner()" id="btn-stop-scan" class="hidden mt-4 bg-red-50 hover:bg-red-100 text-red-600 font-bold py-2.5 px-4 rounded-xl transition-colors">
                            <i class="ph-bold ph-stop-circle inline align-middle mr-1.5"></i> Matikan Kamera
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bagian Etalase Produk -->
    <section id="produk" class="py-12 md:py-16 bg-gray-50 border-t border-gray-100">
        <div class="site-container">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-2xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Katalog Produk Pilihan</h2>
                <p class="text-gray-500 mt-3">Nikmati beragam cita rasa kuliner nusantara instan kualitas restoran terpercaya.</p>
            </div>
            <!-- Filter Bar Kategori Produk -->
            <div class="flex flex-wrap justify-center gap-3 mb-10" id="category-filter-bar">
                <button data-category="semua" class="category-filter-btn active-filter inline-flex items-center gap-1.5 px-5 py-2 rounded-full text-sm font-bold transition-all shadow-sm">
                    <i class="ph-bold ph-squares-four text-base"></i> Semua
                </button>
                <button data-category="makanan" class="category-filter-btn inline-flex items-center gap-1.5 px-5 py-2 rounded-full text-sm font-bold transition-all shadow-sm">
                    <i class="ph-bold ph-bowl-food text-base"></i> Makanan
                </button>
                <button data-category="minuman" class="category-filter-btn inline-flex items-center gap-1.5 px-5 py-2 rounded-full text-sm font-bold transition-all shadow-sm">
                    <i class="ph-bold ph-coffee text-base"></i> Minuman
                </button>
                <button data-category="kebutuhan-rumah-tangga" class="category-filter-btn inline-flex items-center gap-1.5 px-5 py-2 rounded-full text-sm font-bold transition-all shadow-sm">
                    <i class="ph-bold ph-house-line text-base"></i> Kebutuhan Rumah Tangga
                </button>
            </div>
            <div id="public-product-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="col-span-full text-center text-gray-500 py-12">
                    <i class="ph-bold ph-spinner animate-spin text-3xl text-brand mb-2 block mx-auto"></i> Memuat daftar menu...
                </div>
            </div>
        </div>
    </section>

    <!-- Section Hubungi Kami -->
    <section class="reveal-left py-6 md:py-10 bg-white">
        <div class="site-container">
            <div class="w-full bg-gradient-to-r from-brand-light to-white rounded-2xl border border-brand/20 shadow-sm px-6 md:px-8 py-6 flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight mb-1">Ada Pertanyaan?</h2>
                    <p class="text-gray-500 text-sm leading-relaxed max-w-md">Tim kami siap membantu seputar produk dan pemesanan. Hubungi kami langsung via WhatsApp.</p>
                </div>
                <a href="https://wa.me/6281381690100" target="_blank" rel="noopener noreferrer"
                   class="shrink-0 inline-flex items-center justify-center gap-2.5 bg-brand hover:bg-brand-dark text-white font-bold px-8 py-3.5 rounded-xl transition-all shadow-md shadow-brand/20 text-sm">
                    <i class="ph-bold ph-whatsapp-logo text-lg"></i> Hubungi via WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="footer" class="bg-gray-900 text-gray-300">
        <div class="site-container py-14">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

                <!-- Kolom 1: Brand identity -->
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-white rounded-xl p-1.5 shrink-0 flex items-center justify-center w-9 h-9">
                            <img src="assets/img/logo-kwt.png" alt="Logo KWT Mawar Bodas II" class="w-6 h-6 object-contain">
                        </div>
                        <span class="font-black text-white text-lg tracking-tight">KWT Mawar Bodas II</span>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed max-w-xs">
                        Olahan kuliner nusantara instan, higienis, dan terjamin keasliannya lewat teknologi QR Code Dinamis.
                    </p>
                </div>

                <!-- Kolom 2: Navigasi cepat -->
                <div>
                    <h4 class="font-bold text-white text-sm uppercase tracking-widest mb-4">Navigasi</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li>
                            <a href="#produk" class="text-gray-400 hover:text-brand transition-colors flex items-center gap-2">
                                <i class="ph-bold ph-storefront text-base"></i> Katalog Produk
                            </a>
                        </li>
                        <li>
                            <a href="#qrcode" class="text-gray-400 hover:text-brand transition-colors flex items-center gap-2">
                                <i class="ph-bold ph-qr-code text-base"></i> Validasi QR Kemasan
                            </a>
                        </li>
                        <li>
                            <a href="pages/login.php" class="text-gray-400 hover:text-brand transition-colors flex items-center gap-2">
                                <i class="ph-bold ph-sign-in text-base"></i> Login Admin
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Kolom 3: Kontak -->
                <div>
                    <h4 class="font-bold text-white text-sm uppercase tracking-widest mb-4">Kontak</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li>
                            <a href="https://wa.me/6281381690100" target="_blank" rel="noopener noreferrer"
                               class="text-gray-400 hover:text-brand transition-colors flex items-center gap-2">
                                <i class="ph-bold ph-whatsapp-logo text-base"></i> +62 813-8169-0100
                            </a>
                        </li>
                        <li>
                            <a href="https://instagram.com/bundahabib19" target="_blank" rel="noopener noreferrer"
                               class="text-gray-400 hover:text-brand transition-colors flex items-center gap-2">
                                <i class="ph-bold ph-instagram-logo text-base"></i> @bundahabib19
                            </a>
                        </li>
                        <li>
                            <a href="https://facebook.com/bundahabib19" target="_blank" rel="noopener noreferrer"
                               class="text-gray-400 hover:text-brand transition-colors flex items-center gap-2">
                                <i class="ph-bold ph-facebook-logo text-base"></i> bundahabib19
                            </a>
                        </li>
                        <li class="flex items-center gap-2 text-gray-400">
                            <i class="ph-bold ph-map-pin text-base shrink-0"></i>
                            <span>Kelompok Wanita Tani Mawar Bodas II</span>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Garis pemisah + copyright -->
            <div class="border-t border-white/10 mt-10 pt-6 text-center text-xs text-gray-500">
                &copy; <?php echo date('Y'); ?> KWT Mawar Bodas II. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Modal Hasil Scan QR -->
    <div id="modal-qr" class="fixed inset-0 bg-black/80 z-[100] hidden flex items-center justify-center backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl transform scale-95 opacity-0 transition-all duration-300" id="modal-qr-content">
            <!-- Header -->
            <div class="bg-green-600 p-5 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg">Garansi Keaslian Lolos</h3>
                <button onclick="closeQRModal()" class="text-white/80 hover:text-white bg-white/10 p-1.5 rounded-lg"><i class="ph-bold ph-x text-lg"></i></button>
            </div>
            <!-- Body -->
            <div class="p-5">
                <!-- Nama + verified label + status -->
                <div class="text-center mb-5">
                    <h4 id="res-name" class="text-2xl font-black text-gray-900 tracking-tight mb-1">Memuat data...</h4>
                    <!-- Verified: icon shield phosphor + teks uppercase -->
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-600 mb-3 flex items-center justify-center gap-1">
                        <i class="ph-fill ph-seal-check text-sm"></i> Terverifikasi Asli · Habib Snack
                    </p>
                    <!-- Status kedaluwarsa: teks saja tanpa dot -->
                    <div id="res-status-badge" class="flex items-center justify-center gap-1 text-sm font-semibold"></div>
                </div>
                <!-- Divider -->
                <div class="border-t border-gray-100 mb-4"></div>
                <!-- Info rows: 2 kolom -->
                <div class="grid grid-cols-2 gap-x-6 gap-y-3">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-gray-400 text-xs uppercase tracking-wider font-semibold">Nomor Batch</span>
                        <span id="res-batch" class="font-bold text-gray-900 text-sm"></span>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <span class="text-gray-400 text-xs uppercase tracking-wider font-semibold">Tgl. Produksi</span>
                        <span id="res-prod" class="font-bold text-gray-900 text-sm"></span>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <span class="text-gray-400 text-xs uppercase tracking-wider font-semibold">Batas Kedaluwarsa</span>
                        <span id="res-exp" class="font-bold text-gray-900 text-sm"></span>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <span class="text-gray-400 text-xs uppercase tracking-wider font-semibold">Harga</span>
                        <span id="res-price" class="font-bold text-sm"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Generate QR Untuk Stiker Kemasan -->
    <div id="modal-generate-qr" class="fixed inset-0 bg-black/85 z-[120] hidden flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-3xl text-center max-w-sm w-full shadow-2xl">
            <h3 class="font-black text-xl text-gray-900 mb-1">Cetak Kode Batch QR</h3>
            <p id="qr-batch-text" class="text-brand-accent font-bold text-sm bg-brand-accent/10 px-3 py-1 rounded-full border border-brand-accent/20 inline-block mb-6"></p>
            
            <div class="p-4 bg-gray-50 rounded-2xl border mb-6">
                <!-- QR Code Generator Dinamis menggunakan API goqr.me -->
                <img id="qr-generated-img" src="" alt="Dynamic QR" class="w-48 h-48 mx-auto border-4 border-white rounded-xl shadow-md">
            </div>
            
            <p class="text-xs text-gray-500 leading-relaxed mb-6">
                Klik kanan gambar di atas lalu pilih <strong class="text-gray-900">"Simpan Gambar"</strong>. Cetak dan tempelkan QR Code ini ke stiker kemasan produk Anda.
            </p>
            <button onclick="document.getElementById('modal-generate-qr').classList.add('hidden')" class="w-full bg-gray-950 hover:bg-black text-white py-3 rounded-xl font-bold transition-colors">Selesai & Tutup</button>
        </div>
    </div>

    <script>
        <?php include __DIR__ . '/assets/js/main.js'; ?>
    </script>
</body>
</html>
