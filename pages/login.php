<?php
// =========================================================================
// SESSION COOKIE HARDENING — harus sebelum session_start()
// =========================================================================
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

// Cegah browser cache halaman login
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Sudah login? Langsung ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin.php');
    exit;
}

// [SEC-3] Generate CSRF token untuk form login
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../config/database.php';

$error        = '';
$tooManyTries = false;

// =========================================================================
// [SEC-5] Helper rate limiting — berbasis DB, key: username + IP
// =========================================================================
function getClientIp(): string {
    // Hormati X-Forwarded-For jika di belakang reverse proxy, tapi validasi format
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip  = trim($ips[0]);
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function checkRateLimit(PDO $pdo, string $username, string $ip): array {
    $identifier = strtolower(trim($username));
    $stmt = $pdo->prepare(
        "SELECT attempt_count, locked_until FROM login_attempts
         WHERE identifier = ? AND ip_address = ? LIMIT 1"
    );
    $stmt->execute([$identifier, $ip]);
    $row = $stmt->fetch();

    if (!$row) {
        return ['locked' => false, 'attempts' => 0];
    }

    // Cek apakah masih dalam masa lock
    if (!empty($row['locked_until'])) {
        $lockedUntil = new DateTime($row['locked_until']);
        if (new DateTime() < $lockedUntil) {
            return ['locked' => true, 'attempts' => (int)$row['attempt_count'], 'until' => $lockedUntil];
        }
    }

    return ['locked' => false, 'attempts' => (int)$row['attempt_count']];
}

function recordFailedAttempt(PDO $pdo, string $username, string $ip): void {
    $identifier = strtolower(trim($username));
    $stmt = $pdo->prepare(
        "INSERT INTO login_attempts (identifier, ip_address, attempt_count, last_attempt_at)
         VALUES (?, ?, 1, NOW())
         ON DUPLICATE KEY UPDATE
             attempt_count    = attempt_count + 1,
             last_attempt_at  = NOW(),
             locked_until     = IF(attempt_count + 1 >= 5, DATE_ADD(NOW(), INTERVAL 5 MINUTE), NULL)"
    );
    $stmt->execute([$identifier, $ip]);
}

function resetAttempts(PDO $pdo, string $username, string $ip): void {
    $identifier = strtolower(trim($username));
    $pdo->prepare(
        "DELETE FROM login_attempts WHERE identifier = ? AND ip_address = ?"
    )->execute([$identifier, $ip]);
}

// =========================================================================
// PROSES LOGIN
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // [SEC-3] Validasi CSRF token form login
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $error = 'Permintaan tidak valid. Silakan coba lagi.';
    } elseif (isset($db_error)) {
        $error = 'Koneksi database bermasalah. Hubungi administrator.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'Username dan password wajib diisi.';
        } else {
            $clientIp   = getClientIp();
            $rateResult = checkRateLimit($pdo, $username, $clientIp);

            if ($rateResult['locked']) {
                // [SEC-5] Akun terkunci karena terlalu banyak percobaan gagal
                $tooManyTries = true;
            } else {
                $stmt  = $pdo->prepare("SELECT * FROM login WHERE username = ? LIMIT 1");
                $stmt->execute([$username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password'])) {
                    // Login berhasil — reset attempts, update audit trail
                    resetAttempts($pdo, $username, $clientIp);

                    // [DB-1] Update last_login_at dan last_login_ip
                    $pdo->prepare("UPDATE login SET last_login_at = NOW(), last_login_ip = ? WHERE id = ?")
                        ->execute([$clientIp, $admin['id']]);

                    // Regenerate session ID untuk mencegah session fixation
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username']  = $admin['username'];
                    // Regenerate CSRF token setelah login
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                    header('Location: admin.php');
                    exit;
                } else {
                    // [SEC-5] Gagal — catat attempt dan kembalikan pesan generic
                    recordFailedAttempt($pdo, $username, $clientIp);
                    $error = 'Username atau password salah.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Login Admin — KWT Mawar Bodas II</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.cdnfonts.com/css/satoshi" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
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
</head>
<body class="font-sans antialiased">

    <div class="fixed inset-0 z-0">
        <img src="../assets/img/wallpaper.png" alt="" class="w-full h-full object-cover">
        <div class="absolute inset-0" style="background:rgba(255,255,255,0.88);"></div>
    </div>

    <div class="relative z-10 min-h-screen flex flex-col items-center justify-center px-4 py-8">

        <div class="bg-white rounded-3xl shadow-xl border border-gray-200 w-full max-w-md p-8">

            <div class="mb-7 text-center">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Masuk ke Dashboard Admin</h1>
                <p class="text-gray-500 text-sm mt-1.5">Khusus untuk pengelola KWT Mawar Bodas II</p>
            </div>

            <?php if ($tooManyTries): ?>
            <div class="mb-5 flex items-start gap-2.5 bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 text-sm">
                <i class="ph-bold ph-warning-circle text-base shrink-0 mt-0.5"></i>
                <span>Terlalu banyak percobaan gagal. Coba lagi dalam beberapa menit.</span>
            </div>
            <?php elseif ($error !== ''): ?>
            <div class="mb-5 flex items-start gap-2.5 bg-red-50 border border-red-200 text-red-600 rounded-xl px-4 py-3 text-sm">
                <i class="ph-bold ph-warning-circle text-base shrink-0 mt-0.5"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-5" autocomplete="off">
                <!-- [SEC-3] CSRF token tersembunyi -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <div>
                    <label for="username" class="block text-xs font-semibold text-gray-600 mb-1.5">Username</label>
                    <div class="relative">
                        <i class="ph-bold ph-user absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" id="username" name="username" required autocomplete="off"
                            placeholder="Masukkan username"
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-600 mb-1.5">Password</label>
                    <div class="relative">
                        <i class="ph-bold ph-lock-key absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            placeholder="Masukkan password"
                            class="w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-brand/20 focus:border-brand outline-none transition-all">
                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                            tabindex="-1" aria-label="Tampilkan/sembunyikan password">
                            <i id="eye-icon" class="ph-bold ph-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" <?= $tooManyTries ? 'disabled' : '' ?>
                    class="w-full bg-brand hover:bg-brand-dark disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-3 rounded-xl transition-all text-sm flex items-center justify-center gap-2 shadow-md shadow-brand/20 mt-2">
                    <i class="ph-bold ph-sign-in text-base"></i> Masuk
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Butuh bantuan?
                <a href="https://wa.me/6281381690100" target="_blank" rel="noopener noreferrer"
                   class="text-brand font-semibold hover:underline">Klik disini</a>
            </p>
        </div>

        <a href="../index.php" class="mt-6 flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition-colors">
            <i class="ph-bold ph-arrow-left text-sm"></i> Kembali ke Beranda
        </a>
    </div>

    <script>
        function togglePassword() {
            const input   = document.getElementById('password');
            const icon    = document.getElementById('eye-icon');
            const visible = input.type === 'text';
            input.type    = visible ? 'password' : 'text';
            icon.className = visible ? 'ph-bold ph-eye text-sm' : 'ph-bold ph-eye-slash text-sm';
        }
    </script>
</body>
</html>
