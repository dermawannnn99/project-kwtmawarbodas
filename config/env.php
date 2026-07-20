<?php
/**
 * Parser .env ringan — tidak butuh Composer.
 * Membaca file .env dari root project dan mengisi $_ENV / getenv().
 */
function loadEnv(string $path): void {
    if (!file_exists($path)) {
        error_log("[ENV] File .env tidak ditemukan di: $path");
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;

        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) continue;

        $key   = trim($parts[0]);
        $value = trim($parts[1]);

        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key]  = $value;
            putenv("$key=$value");
        }
    }
}

/**
 * Deteksi apakah koneksi saat ini HTTPS — termasuk lewat reverse proxy/ngrok.
 * Cek dari multiple sumber agar reliable di semua environment.
 */
function isSecureConnection(): bool {
    // Koneksi langsung HTTPS
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    // Lewat reverse proxy / ngrok — header X-Forwarded-Proto
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        return strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
    }
    // Cloudflare / proxy lain
    if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on') {
        return true;
    }
    // Port 443
    if (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) {
        return true;
    }
    // Request scheme (beberapa server set ini)
    if (!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https') {
        return true;
    }
    // Ngrok domain detection — ngrok selalu HTTPS
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (str_ends_with($host, '.ngrok.io') || str_ends_with($host, '.ngrok-free.app') || str_ends_with($host, '.ngrok.app')) {
        return true;
    }
    return false;
}

/**
 * Kembalikan nilai SameSite yang tepat berdasarkan koneksi.
 * HTTPS: 'None' agar cookie bisa dikirim cross-site (wajib untuk ngrok + iOS Safari).
 * HTTP : 'Lax' cukup untuk local dev.
 */
function sessionSameSite(): string {
    return isSecureConnection() ? 'None' : 'Lax';
}
