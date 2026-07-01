<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$nama = mysqli_real_escape_string($koneksi, $data['nama_produk']);
$kategori = intval($data['kategori_id']);
$deskripsi = mysqli_real_escape_string($koneksi, $data['deskripsi']);
$harga = intval($data['harga']);
$stok = intval($data['stok']);

$query = "INSERT INTO products (nama_produk, kategori_id, deskripsi, harga, stok) 
          VALUES ('$nama', '$kategori', '$deskripsi', '$harga', '$stok')";

if ($koneksi->query($query)) {
    echo json_encode(['success' => true, 'message' => 'Produk berhasil ditambahkan', 'id' => $koneksi->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menambahkan produk']);
}
?>