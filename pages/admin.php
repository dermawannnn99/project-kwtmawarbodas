<?php
// SESSION COOKIE HARDENING
require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => isSecureConnection(),
    'httponly' => true,
    'samesite' => sessionSameSite(),
]);
session_start();

// Session guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// [SEC-3] Pastikan CSRF token tersedia
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Security headers
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
// [SEC-6] CSP — izinkan CDN yang dipakai (Tailwind, Phosphor, Satoshi)
header("Content-Security-Policy: default-src 'self'; " .
    "script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://unpkg.com; " .
    "style-src 'self' 'unsafe-inline' https://fonts.cdnfonts.com https://fonts.googleapis.com https://unpkg.com https://cdn.jsdelivr.net; " .
    "font-src 'self' https://fonts.cdnfonts.com https://fonts.gstatic.com https://unpkg.com https://cdn.jsdelivr.net; " .
    "img-src 'self' data: https: blob:; " .
    "connect-src 'self' https://api.qrserver.com;");

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/admin-api.php';
?>
<?php require_once __DIR__ . '/admin-head.php'; ?>
<?php require_once __DIR__ . '/admin-sidebar.php'; ?>

<div class="lg:pl-64 min-h-screen flex flex-col">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-20">
        <div class="px-4 sm:px-6 lg:px-8 h-14 flex items-center gap-3">
            <button onclick="openSidebar()" class="lg:hidden p-2 rounded-xl hover:bg-gray-100 text-gray-500 transition-colors">
                <i class="ph-bold ph-list text-xl"></i>
            </button>
            <span class="font-semibold text-gray-900 text-sm" id="topbar-title">Ringkasan</span>
        </div>
    </header>

    <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
<?php require_once __DIR__ . '/admin-section-ringkasan.php'; ?>
<?php require_once __DIR__ . '/admin-section-tambah.php'; ?>
<?php require_once __DIR__ . '/admin-section-inventory.php'; ?>
    </main>
</div>

<!-- Modal Generate QR -->
<div id="modal-generate-qr" class="fixed inset-0 bg-black/80 z-[120] hidden flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-3xl text-center max-w-sm w-full shadow-2xl">
        <div class="bg-green-50 w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="ph-bold ph-qr-code text-green-600 text-3xl"></i>
        </div>
        <h3 class="font-black text-xl text-gray-900 mb-1">Cetak Kode Batch QR</h3>
        <p id="qr-batch-text" class="text-brand font-bold text-sm bg-amber-50 px-3 py-1 rounded-full border border-amber-200 inline-block mb-5"></p>
        <div class="p-3 bg-gray-50 rounded-2xl border mb-5">
            <img id="qr-generated-img" src="" alt="Dynamic QR" class="w-48 h-48 mx-auto border-4 border-white rounded-xl shadow-md" crossorigin="anonymous">
        </div>
        <p class="text-xs text-gray-500 leading-relaxed mb-5">
            Klik kanan gambar lalu pilih <strong class="text-gray-900">"Simpan Gambar"</strong>, cetak dan tempelkan ke kemasan produk.
        </p>
        <canvas id="qr-download-canvas" class="hidden"></canvas>
        <div class="flex flex-col gap-2.5">
            <button onclick="downloadQR()"
                class="w-full bg-brand hover:bg-brand-dark text-white py-3 rounded-xl font-bold transition-colors text-sm flex items-center justify-center gap-2">
                <i class="ph-bold ph-download-simple"></i> Download Gambar QR
            </button>
            <button onclick="document.getElementById('modal-generate-qr').classList.add('hidden')"
                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-bold transition-colors text-sm">
                Tutup
            </button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/admin-modal-delete.php'; ?>

<div id="notif-backdrop"
     class="notif-backdrop fixed inset-0 z-[300] hidden items-center justify-center p-6"
     style="background:rgba(0,0,0,0.55);">
    <div id="notif-card" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg px-10 py-8 text-center">
        <div id="notif-icon-wrap" class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <i id="notif-icon" class="text-4xl"></i>
        </div>
        <p id="notif-title" class="font-black text-xl text-gray-900 mb-2"></p>
        <p id="notif-desc"  class="text-gray-500 text-sm leading-relaxed"></p>
    </div>
</div>

<!-- [QUAL-1] shared-utils harus dimuat pertama sebelum render.js dan products.js -->
<script src="../assets/js/shared-utils.js"></script>
<script src="admin-js/ui.js"></script>
<script src="admin-js/render.js"></script>
<script src="admin-js/products.js"></script>
<script src="admin-js/qr.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        fetchProducts();
        resetForm();
    });
</script>
</body>
</html>
