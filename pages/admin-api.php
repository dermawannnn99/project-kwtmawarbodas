<?php
// =========================================================================
// SESSION COOKIE HARDENING — harus sebelum session_start()
// =========================================================================
require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => isSecureConnection(),
        'httponly' => true,
        'samesite' => sessionSameSite(),
    ]);
    session_start();
}

// SESSION GUARD
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Silakan login terlebih dahulu.']);
    exit;
}

// [SEC-3] CSRF token — generate jika belum ada di session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../config/database.php';

// =========================================================================
// API HANDLER
// =========================================================================
if (!isset($_GET['action'])) {
    return; // Dipanggil tanpa action — dari include admin.php, tidak perlu respons
}

header('Content-Type: application/json');

if (isset($db_error)) {
    error_log("[API] DB error: $db_error");
    echo json_encode(['status' => 'error', 'message' => 'Koneksi database bermasalah.']);
    exit;
}

$action = $_GET['action'];

// GET ALL PRODUCTS — read-only, CSRF tidak wajib
if ($action === 'get_products') {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    echo json_encode(['status' => 'success', 'data' => $stmt->fetchAll()]);
    exit;
}

// =========================================================================
// [SEC-3] CSRF VALIDATION — wajib untuk semua action mutasi data (POST)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
    if (!hash_equals($_SESSION['csrf_token'], (string)$clientToken)) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid (CSRF token mismatch).']);
        exit;
    }
}

// =========================================================================
// SAVE PRODUCT (CREATE / UPDATE)
// =========================================================================
if ($action === 'save_product') {
    $id          = trim($_POST['id'] ?? '');
    $name        = trim($_POST['name'] ?? '');
    $price       = trim($_POST['price'] ?? '');
    $badge       = trim($_POST['badge'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $batch_code  = trim($_POST['batch_code'] ?? '');
    $prod_date   = trim($_POST['prod_date'] ?? '');
    $exp_date    = trim($_POST['exp_date'] ?? '');
    $image_url   = trim($_POST['existing_image'] ?? '');

    // [SEC-2] Validasi field wajib
    if ($name === '' || $description === '' || $batch_code === '' || $prod_date === '' || $exp_date === '') {
        echo json_encode(['status' => 'error', 'message' => 'Semua field wajib harus diisi.']);
        exit;
    }
    if (!is_numeric($price) || (float)$price <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Harga harus berupa angka positif.']);
        exit;
    }

    // [SEC-2] Validasi format tanggal ketat
    $dtProd = DateTime::createFromFormat('Y-m-d', $prod_date);
    $dtExp  = DateTime::createFromFormat('Y-m-d', $exp_date);
    if (!$dtProd || $dtProd->format('Y-m-d') !== $prod_date ||
        !$dtExp  || $dtExp->format('Y-m-d')  !== $exp_date) {
        echo json_encode(['status' => 'error', 'message' => 'Format tanggal tidak valid (gunakan YYYY-MM-DD).']);
        exit;
    }
    if ($dtExp < $dtProd) {
        echo json_encode(['status' => 'error', 'message' => 'Tanggal kadaluarsa tidak boleh lebih awal dari tanggal produksi.']);
        exit;
    }

    // [SEC-2] Sanitasi batch_code — hanya alfanumerik dan strip/dash
    if (!preg_match('/^[A-Za-z0-9\-]+$/', $batch_code)) {
        echo json_encode(['status' => 'error', 'message' => 'Kode batch hanya boleh berisi huruf, angka, dan tanda hubung (-).']);
        exit;
    }

    // [SEC-6] Defense-in-depth: strip tag HTML dari name dan description sebelum disimpan
    $name        = strip_tags($name);
    $description = strip_tags($description);
    $badge       = strip_tags($badge);

    // [SEC-1] Validasi upload file gambar
    if (isset($_FILES['product_image'])) {
        $fileErr = $_FILES['product_image']['error'];

        // Tangani error dari php.ini (file terlalu besar di level server)
        if ($fileErr === UPLOAD_ERR_INI_SIZE || $fileErr === UPLOAD_ERR_FORM_SIZE) {
            echo json_encode(['status' => 'error', 'message' => 'File gambar terlalu besar. Maksimal 5 MB.']);
            exit;
        }

        if ($fileErr === UPLOAD_ERR_OK) {
            $file     = $_FILES['product_image'];
            $tmpPath  = $file['tmp_name'];
            $origName = $file['name'];

            // [SEC-1] Validasi ukuran di backend (tidak hanya di JS)
            if ($file['size'] > 5 * 1024 * 1024) {
                echo json_encode(['status' => 'error', 'message' => 'File gambar melebihi batas 5 MB.']);
                exit;
            }

            // Validasi MIME type dan ekstensi
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $tmpPath);
            finfo_close($finfo);
            $allowedMime = ['image/jpeg', 'image/png'];
            $allowedExt  = ['jpg', 'jpeg', 'png'];
            $ext         = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            if (!in_array($mimeType, $allowedMime) || !in_array($ext, $allowedExt)) {
                echo json_encode(['status' => 'error', 'message' => 'Format gambar tidak valid. Hanya JPG dan PNG.']);
                exit;
            }

            $uniqueName = uniqid('prod_', true) . '.' . $ext;
            $uploadDir  = __DIR__ . '/../uploads/';

            if (!move_uploaded_file($tmpPath, $uploadDir . $uniqueName)) {
                error_log("[UPLOAD] Gagal memindahkan file ke: $uploadDir$uniqueName");
                echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan gambar ke server.']);
                exit;
            }

            // [BUG-1] Hapus gambar lama jika ada gambar baru dan lama adalah file lokal
            $oldImageUrl = $image_url; // existing_image dari form
            if (!empty($id) && !empty($oldImageUrl)) {
                if (!str_starts_with($oldImageUrl, 'http://') && !str_starts_with($oldImageUrl, 'https://')) {
                    $oldFilePath = __DIR__ . '/../' . $oldImageUrl;
                    if (file_exists($oldFilePath) && is_writable($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
            }

            $image_url = 'uploads/' . $uniqueName;
        } elseif ($fileErr !== UPLOAD_ERR_NO_FILE) {
            error_log("[UPLOAD] Error upload file, kode: $fileErr");
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat mengunggah gambar.']);
            exit;
        }
    }

    if (empty($id) && empty($image_url)) {
        echo json_encode(['status' => 'error', 'message' => 'Gambar produk wajib diisi untuk produk baru.']);
        exit;
    }

    try {
        if (empty($id)) {
            $stmt = $pdo->prepare(
                "INSERT INTO products (name, price, image_url, badge, description, batch_code, prod_date, exp_date)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$name, (float)$price, $image_url, $badge, $description, $batch_code, $prod_date, $exp_date]);
            echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan.']);
        } else {
            $id = (int)$id;
            $stmt = $pdo->prepare(
                "UPDATE products SET name=?, price=?, image_url=?, badge=?, description=?, batch_code=?, prod_date=?, exp_date=?
                 WHERE id=?"
            );
            $stmt->execute([$name, (float)$price, $image_url, $badge, $description, $batch_code, $prod_date, $exp_date, $id]);
            echo json_encode(['status' => 'success', 'message' => 'Data produk berhasil diperbarui.']);
        }
    } catch (\PDOException $err) {
        error_log("[DB] save_product error: " . $err->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Kode batch sudah digunakan produk lain.']);
    }
    exit;
}

// =========================================================================
// DELETE PRODUCT
// =========================================================================
if ($action === 'delete_product') {
    // [SEC-2] [BUG-2] Validasi ID ketat
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID produk tidak valid.']);
        exit;
    }

    try {
        // [BUG-1] Ambil image_url sebelum dihapus, lalu unlink jika file lokal
        $row = $pdo->prepare("SELECT image_url FROM products WHERE id=?");
        $row->execute([$id]);
        $product = $row->fetch();

        if (!$product) {
            // [BUG-2] Produk tidak ditemukan
            echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan.']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan.']);
            exit;
        }

        // Hapus file gambar lokal jika ada
        $imageUrl = $product['image_url'] ?? '';
        if (!empty($imageUrl) && !str_starts_with($imageUrl, 'http://') && !str_starts_with($imageUrl, 'https://')) {
            $filePath = __DIR__ . '/../' . $imageUrl;
            if (file_exists($filePath) && is_writable($filePath)) {
                unlink($filePath);
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Produk berhasil dihapus.']);
    } catch (\PDOException $err) {
        error_log("[DB] delete_product error: " . $err->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus produk.']);
    }
    exit;
}

// =========================================================================
// TOGGLE VISIBILITY
// =========================================================================
if ($action === 'toggle_visibility') {
    $id         = (int)($_POST['id'] ?? 0);
    $is_visible = (int)($_POST['is_visible'] ?? 0);
    $is_visible = ($is_visible === 1) ? 1 : 0; // pastikan hanya 0 atau 1

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID produk tidak valid.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE products SET is_visible=? WHERE id=?");
        $stmt->execute([$is_visible, $id]);
        $label = $is_visible ? 'Produk ditampilkan di katalog.' : 'Produk disembunyikan dari katalog.';
        echo json_encode(['status' => 'success', 'message' => $label]);
    } catch (\PDOException $err) {
        error_log("[DB] toggle_visibility error: " . $err->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah visibilitas produk.']);
    }
    exit;
}
