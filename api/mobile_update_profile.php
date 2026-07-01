<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$user_id = intval($data['user_id']);
$nama = mysqli_real_escape_string($koneksi, $data['nama_lengkap']);
$no_hp = mysqli_real_escape_string($koneksi, $data['no_hp']);
$alamat = mysqli_real_escape_string($koneksi, $data['alamat']);

$query = "UPDATE users SET nama_lengkap = '$nama', no_hp = '$no_hp', alamat = '$alamat' WHERE id = $user_id";

if ($koneksi->query($query)) {
    echo json_encode(['success' => true, 'message' => 'Profil berhasil diperbarui']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui profil']);
}
?>