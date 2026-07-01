<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$product_id = intval($data['product_id']);

// Hapus gambar jika ada
$gambar = $koneksi->query("SELECT gambar FROM products WHERE id = $product_id")->fetch_assoc();
if ($gambar && $gambar['gambar'] && file_exists("../uploads/produk/" . $gambar['gambar'])) {
    unlink("../uploads/produk/" . $gambar['gambar']);
}

$query = "DELETE FROM products WHERE id = $product_id";

if ($koneksi->query($query)) {
    echo json_encode(['success' => true, 'message' => 'Produk berhasil dihapus']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menghapus produk']);
}
?>