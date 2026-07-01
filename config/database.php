<?php
// ============================================
// KONFIGURASI DATABASE - WARUNG MAKAN RIZAL
// ============================================

// Mengaktifkan session untuk login
session_start();

// Menampilkan error untuk debugging (matiikan saat production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone ke WIB (Asia/Jakarta)
date_default_timezone_set('Asia/Jakarta');

// ============================================
// KONFIGURASI DATABASE (LOCALHOST / XAMPP)
// ============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'warung_makan_rizal');

// ============================================
// KONEKSI DATABASE
// ============================================
$koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// Set charset ke UTF-8
$koneksi->set_charset("utf8mb4");

// ============================================
// BASE URL (Sesuaikan dengan folder proyek Anda)
// ============================================
// Jika project di root htdocs:
// define('BASE_URL', 'http://localhost/');
// 
// Jika project di folder warung_makan_rizal:
define('BASE_URL', 'http://localhost/warung_makan_rizal/');

// ============================================
// FUNGSI HELPER
// ============================================

// Format Rupiah
function format_rupiah($angka) {
    if ($angka == null || $angka == 0) {
        return "Rp 0";
    }
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Ambil jumlah item di keranjang
function get_cart_count($customer_id) {
    global $koneksi;
    $result = $koneksi->query("SELECT SUM(quantity) as total FROM cart WHERE customer_id = $customer_id");
    $data = $result->fetch_assoc();
    return $data['total'] ?? 0;
}

// Kirim notifikasi ke user
function send_notification($user_id, $title, $message, $type = 'system') {
    global $koneksi;
    $stmt = $koneksi->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $title, $message, $type);
    return $stmt->execute();
}

// Ambil jumlah notifikasi yang belum dibaca
function get_unread_notifications($user_id) {
    global $koneksi;
    $result = $koneksi->query("SELECT COUNT(*) as total FROM notifications WHERE user_id = $user_id AND is_read = 0");
    $data = $result->fetch_assoc();
    return $data['total'] ?? 0;
}

// Generate order number unik
function generate_order_number() {
    return 'ORD' . date('ymd') . rand(1000, 9999);
}

// Upload gambar
function upload_gambar($file, $folder, $old_file = null) {
    $target_dir = "../uploads/$folder/";
    
    // Buat folder jika belum ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Hapus file lama jika ada
    if ($old_file && file_exists($target_dir . $old_file)) {
        unlink($target_dir . $old_file);
    }
    
    // Generate nama file unik
    $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $nama_file = time() . '_' . uniqid() . '.' . $ekstensi;
    $target_file = $target_dir . $nama_file;
    
    // Validasi ekstensi
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ekstensi, $allowed)) {
        return ['success' => false, 'message' => 'Format file tidak didukung'];
    }
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'filename' => $nama_file];
    } else {
        return ['success' => false, 'message' => 'Gagal upload file'];
    }
}

// ============================================
// CEK KONEKSI DATABASE (Opsional)
// ============================================
// Hapus komentar di bawah untuk cek koneksi
// echo "Koneksi database berhasil!";
// echo "<br>Database: " . DB_NAME;
// echo "<br>Host: " . DB_HOST;
?>