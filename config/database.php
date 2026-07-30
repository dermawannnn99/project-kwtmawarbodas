<?php
// =========================================================================
// KONFIGURASI DATABASE MYSQL
// Kredensial dibaca dari file .env di root project — JANGAN hardcode di sini.
// Salin .env.example ke .env dan isi sesuai environment Anda.
// =========================================================================

require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../.env');

$host    = getenv('DB_HOST') ?: 'localhost';
$db      = getenv('DB_NAME') ?: 'lezatpack_db';
$user    = getenv('DB_USER') ?: 'root';
$pass    = getenv('DB_PASS') ?: '';
$charset = 'utf8mb4';
$appEnv  = getenv('APP_ENV') ?: 'production';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Hubungkan dulu ke MySQL host (tanpa memilih database) untuk auto-create DB
    $dsn_no_db = "mysql:host=$host;charset=$charset";
    $pdo_init  = new PDO($dsn_no_db, $user, $pass, $options);
    $pdo_init->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // Hubungkan ke database yang sesungguhnya
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Auto-create tabel products — skema lengkap termasuk is_visible
    // [BUG-3] Kolom is_visible sudah masuk ke definisi tabel, tidak perlu ALTER per-request
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        name        VARCHAR(255) NOT NULL,
        price       DECIMAL(10,2) NOT NULL,
        image_url   TEXT,
        badge       VARCHAR(50),
        description TEXT,
        batch_code  VARCHAR(50) UNIQUE,
        prod_date   DATE,
        exp_date    DATE,
        is_visible  TINYINT(1) NOT NULL DEFAULT 1,
        category    VARCHAR(30) NOT NULL DEFAULT 'makanan',
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Migrasi is_visible hanya di local/dev untuk database lama yang belum punya kolom ini
    // [PERF-1] Di production (APP_ENV=production), blok ini dilewati — skema dianggap final
    if ($appEnv !== 'production') {
        $cols = $pdo->query("SHOW COLUMNS FROM products LIKE 'is_visible'")->fetchAll();
        if (empty($cols)) {
            $pdo->exec("ALTER TABLE products ADD COLUMN is_visible TINYINT(1) NOT NULL DEFAULT 1");
            $pdo->exec("UPDATE products SET is_visible = 1");
        }
    }

    // Migrasi kolom category — SELALU dieksekusi di semua environment (local maupun production)
    // [CAT-1] Database production yang sudah berjalan belum punya kolom ini — tidak boleh di-skip
    $catCols = $pdo->query("SHOW COLUMNS FROM products LIKE 'category'")->fetchAll();
    if (empty($catCols)) {
        $pdo->exec("ALTER TABLE products ADD COLUMN category VARCHAR(30) NOT NULL DEFAULT 'makanan'");
    }

    // Auto-create tabel login — [DB-1] tambah last_login_at dan last_login_ip
    $pdo->exec("CREATE TABLE IF NOT EXISTS login (
        id             INT AUTO_INCREMENT PRIMARY KEY,
        username       VARCHAR(50) NOT NULL UNIQUE,
        password       VARCHAR(255) NOT NULL,
        last_login_at  TIMESTAMP NULL DEFAULT NULL,
        last_login_ip  VARCHAR(45) NULL DEFAULT NULL
    )");

    // Migrasi kolom audit login untuk database lama (hanya di local/dev)
    if ($appEnv !== 'production') {
        $loginCols = $pdo->query("SHOW COLUMNS FROM login")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('last_login_at', $loginCols)) {
            $pdo->exec("ALTER TABLE login ADD COLUMN last_login_at TIMESTAMP NULL DEFAULT NULL");
        }
        if (!in_array('last_login_ip', $loginCols)) {
            $pdo->exec("ALTER TABLE login ADD COLUMN last_login_ip VARCHAR(45) NULL DEFAULT NULL");
        }
    }

    // Auto-create tabel login_attempts — [SEC-5] rate limiting persisten
    $pdo->exec("CREATE TABLE IF NOT EXISTS login_attempts (
        id              INT AUTO_INCREMENT PRIMARY KEY,
        identifier      VARCHAR(100) NOT NULL,
        ip_address      VARCHAR(45) NOT NULL,
        attempt_count   INT NOT NULL DEFAULT 0,
        last_attempt_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        locked_until    TIMESTAMP NULL DEFAULT NULL,
        INDEX idx_identifier_ip (identifier, ip_address)
    )");

    // Seed default admin kalau tabel login kosong
    $adminCount = $pdo->query("SELECT COUNT(*) FROM login")->fetchColumn();
    if ($adminCount == 0) {
        $defaultHash = password_hash('admin123', PASSWORD_BCRYPT);
        $pdo->prepare("INSERT INTO login (username, password) VALUES (?, ?)")
            ->execute(['admin', $defaultHash]);
    }

    // Seed produk dummy jika tabel kosong — [QUAL-4] gambar pakai placeholder lokal
    $check_empty = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($check_empty == 0) {
        $placeholderImg = 'assets/img/gambarhero.png';
        $stmt_seed = $pdo->prepare(
            "INSERT INTO products (name, price, image_url, badge, description, batch_code, prod_date, exp_date)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt_seed->execute(["Rendang Daging Sapi", 65000, $placeholderImg, "Best Seller",
            "Daging sapi pilihan berbalut bumbu rempah asli Minang yang pekat. Dimasak perlahan untuk memastikan bumbu meresap sempurna.",
            "LZT-8742", "2026-05-01", "2026-11-01"]);
        $stmt_seed->execute(["Ayam Woku Belanga", 45000, $placeholderImg, "Pedas Gurih",
            "Potongan daging ayam lembut dengan balutan bumbu kuning pedas kemangi khas Manado yang menggugah selera.",
            "LZT-3129", "2026-05-10", "2026-11-10"]);
        $stmt_seed->execute(["Sambal Goreng Ati", 35000, $placeholderImg, "Favorit",
            "Kombinasi ati ampela ayam segar dan kentang dadu yang digoreng dengan bumbu balado tradisional bercita rasa manis-pedas.",
            "LZT-1094", "2026-05-15", "2026-11-15"]);
    }

} catch (\PDOException $e) {
    error_log("[DB] Koneksi database gagal: " . $e->getMessage());
    $db_error = "Koneksi database gagal. Periksa konfigurasi server.";

    // Persist error to workspace logs for easier debugging from mobile devices
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
    $errFile = $logDir . '/db_error.log';
    $msg = date('c') . " | DB ERROR: " . $e->getMessage() . " | TRACE: " . $e->getTraceAsString() . PHP_EOL;
    file_put_contents($errFile, $msg, FILE_APPEND);
}
