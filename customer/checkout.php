<?php
include '../config/database.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Ambil keranjang
$cart_items = $koneksi->query("SELECT c.*, p.nama_produk, p.harga 
                               FROM cart c 
                               JOIN products p ON c.product_id = p.id 
                               WHERE c.customer_id = $customer_id");

if ($cart_items->num_rows == 0) {
    header("Location: keranjang.php");
    exit();
}

$total_harga = 0;
$items = [];
while ($item = $cart_items->fetch_assoc()) {
    $subtotal = $item['quantity'] * $item['harga'];
    $total_harga += $subtotal;
    $items[] = $item;
}

// Biaya pengiriman (contoh: Rp 5.000 - Rp 15.000 tergantung jarak)
$biaya_pengiriman = 5000;
$grand_total = $total_harga + $biaya_pengiriman;

// Proses checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $payment_method = mysqli_real_escape_string($koneksi, $_POST['payment_method']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    
    // Generate order number
    $order_number = generate_order_number();
    
    // Insert ke orders
    $query = "INSERT INTO orders (order_number, customer_id, total_harga, biaya_pengiriman, alamat_pengiriman, payment_method, catatan, status) 
              VALUES ('$order_number', $customer_id, $grand_total, $biaya_pengiriman, '$alamat', '$payment_method', '$catatan', 'pending')";
    
    if ($koneksi->query($query)) {
        $order_id = $koneksi->insert_id;
        
        // Insert detail pesanan
        foreach ($items as $item) {
            $subtotal = $item['quantity'] * $item['harga'];
            $koneksi->query("INSERT INTO order_details (order_id, product_id, quantity, harga_per_item, subtotal) 
                             VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, {$item['harga']}, $subtotal)");
        }
        
        // Hapus keranjang
        $koneksi->query("DELETE FROM cart WHERE customer_id = $customer_id");
        
        // Notifikasi ke admin
        send_notification(1, "Pesanan Baru", "Ada pesanan baru #$order_number dari " . $_SESSION['customer_nama'], 'order');
        
        echo "<script>alert('Pesanan berhasil!'); window.location.href='pesanan_saya.php';</script>";
        exit();
    } else {
        $error = "Gagal memproses pesanan: " . $koneksi->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Warung Makan Rizal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6F4E37; --secondary: #D4AF37; }
        body { background: #f5f5f5; }
        .sidebar {
            background: #2D1F1A;
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 10px;
            margin: 5px 10px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            color: #D4AF37;
        }
        .sidebar .nav-link i { width: 25px; margin-right: 8px; }
        .navbar-custom {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
        }
        .btn-order {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            border: none;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            color: white;
        }
        .btn-order:hover {
            background: linear-gradient(135deg, #A0522D, #6F4E37);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="text-center py-4 border-bottom border-secondary">
                    <i class="fas fa-utensils fa-3x" style="color: #D4AF37;"></i>
                    <h5 class="mt-2"><?php echo $_SESSION['customer_nama']; ?></h5>
                    <small>Customer</small>
                </div>
                <nav class="nav flex-column mt-3">
                    <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a class="nav-link" href="menu.php"><i class="fas fa-utensils"></i> Menu Makanan</a>
                    <a class="nav-link" href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang</a>
                    <a class="nav-link active" href="pesanan_saya.php"><i class="fas fa-history"></i> Pesanan Saya</a>
                    <a class="nav-link" href="profil.php"><i class="fas fa-user"></i> Profil</a>
                    <hr class="bg-secondary mx-3">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-0">
                <nav class="navbar navbar-custom px-4 py-3 text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Checkout</h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                </nav>
                
                <div class="p-4">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-7">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" id="checkoutForm">
                                        <div class="mb-3">
                                            <label class="form-label">Alamat Lengkap</label>
                                            <textarea name="alamat" class="form-control" rows="3" required><?php echo $_SESSION['customer_alamat']; ?></textarea>
                                            <small class="text-muted">Pastikan alamat Anda benar untuk pengiriman</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Metode Pembayaran</label>
                                            <select name="payment_method" class="form-select" required>
                                                <option value="cash">Bayar di Tempat (Cash)</option>
                                                <option value="transfer">Transfer Bank</option>
                                                <option value="qris">QRIS (Scan QR Code)</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Catatan (opsional)</label>
                                            <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Tolong jangan pakai plastik, dll"></textarea>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-5">
                            <div class="card shadow-sm">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0"><i class="fas fa-receipt"></i> Ringkasan Pesanan</h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach($items as $item): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><?php echo $item['nama_produk']; ?> x<?php echo $item['quantity']; ?></span>
                                        <span><?php echo format_rupiah($item['quantity'] * $item['harga']); ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal</span>
                                        <span><?php echo format_rupiah($total_harga); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Biaya Pengiriman</span>
                                        <span><?php echo format_rupiah($biaya_pengiriman); ?></span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-3">
                                        <strong>Total</strong>
                                        <strong style="color: #6F4E37;"><?php echo format_rupiah($grand_total); ?></strong>
                                    </div>
                                    
                                    <button type="submit" form="checkoutForm" class="btn btn-order">
                                        <i class="fas fa-check-circle"></i> Konfirmasi Pesanan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>