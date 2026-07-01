<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$notif_id = intval($data['notif_id']);

$user_id = $_SESSION['customer_id'] ?? $_SESSION['kurir_id'] ?? $_SESSION['admin_id'] ?? null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User tidak ditemukan']);
    exit();
}

if ($notif_id > 0) {
    $koneksi->query("UPDATE notifications SET is_read = 1 WHERE id = $notif_id AND user_id = $user_id");
} else {
    // Mark all as read
    $koneksi->query("UPDATE notifications SET is_read = 1 WHERE user_id = $user_id");
}

echo json_encode(['success' => true, 'message' => 'Notifikasi ditandai sudah dibaca']);
?>