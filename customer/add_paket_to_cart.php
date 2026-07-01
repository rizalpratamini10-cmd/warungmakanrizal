<?php
include '../config/database.php';

// Data paket (ID khusus 9001-9005)
$paket_list = [
    'ayam' => ['id' => 9001, 'nama' => '🍗 Paket Ayam', 'harga' => 20000],
    'ikan' => ['id' => 9002, 'nama' => '🐟 Paket Ikan', 'harga' => 22000],
    'nasgor' => ['id' => 9003, 'nama' => '🍛 Paket Nasi Goreng', 'harga' => 18000],
    'soto' => ['id' => 9004, 'nama' => '🍜 Paket Soto', 'harga' => 20000],
    'keluarga' => ['id' => 9005, 'nama' => '👨‍👩‍👧‍👦 Paket Keluarga', 'harga' => 70000]
];

// Cek login
if (!isset($_SESSION['customer_id'])) {
    $_SESSION['redirect_after_login'] = 'add_paket_to_cart.php?paket=' . $_GET['paket'];
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$paket_key = $_GET['paket'] ?? '';

if (!isset($paket_list[$paket_key])) {
    header("Location: ../index.php");
    exit();
}

$paket = $paket_list[$paket_key];

// Cek apakah sudah ada di keranjang
$check = $koneksi->query("SELECT id, quantity FROM cart WHERE customer_id = $customer_id AND product_id = {$paket['id']}");

if ($check->num_rows > 0) {
    $cart = $check->fetch_assoc();
    $new_qty = $cart['quantity'] + 1;
    $koneksi->query("UPDATE cart SET quantity = $new_qty WHERE id = {$cart['id']}");
    $message = "Jumlah {$paket['nama']} ditambah menjadi {$new_qty}";
} else {
    $koneksi->query("INSERT INTO cart (customer_id, product_id, quantity) VALUES ($customer_id, {$paket['id']}, 1)");
    $message = "{$paket['nama']} berhasil ditambahkan ke keranjang!";
}

echo "<script>
    alert('$message');
    window.location.href = 'menu.php';
</script>";
exit();
?>