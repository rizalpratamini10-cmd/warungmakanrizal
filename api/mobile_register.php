<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$nama = mysqli_real_escape_string($koneksi, $data['nama_lengkap']);
$email = mysqli_real_escape_string($koneksi, $data['email']);
$no_hp = mysqli_real_escape_string($koneksi, $data['no_hp']);
$alamat = mysqli_real_escape_string($koneksi, $data['alamat']);
$password = password_hash($data['password'], PASSWORD_DEFAULT);

// Cek email sudah terdaftar
$check = $koneksi->query("SELECT id FROM users WHERE email = '$email'");
if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email sudah terdaftar!']);
    exit();
}

$query = "INSERT INTO users (username, password, nama_lengkap, email, no_hp, alamat, role) 
          VALUES ('$email', '$password', '$nama', '$email', '$no_hp', '$alamat', 'customer')";

if ($koneksi->query($query)) {
    echo json_encode(['success' => true, 'message' => 'Pendaftaran berhasil! Silakan login.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mendaftar: ' . $koneksi->error]);
}
?>