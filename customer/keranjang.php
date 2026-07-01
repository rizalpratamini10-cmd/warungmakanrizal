<?php
include '../config/database.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Proses update jumlah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = intval($_POST['quantity']);
    if ($quantity <= 0) {
        $koneksi->query("DELETE FROM cart WHERE id = $cart_id");
    } else {
        $koneksi->query("UPDATE cart SET quantity = $quantity WHERE id = $cart_id");
    }
}

// Proses hapus item
if (isset($_GET['hapus'])) {
    $cart_id = intval($_GET['hapus']);
    $koneksi->query("DELETE FROM cart WHERE id = $cart_id");
    header("Location: keranjang.php");
    exit();
}

// Proses kosongkan keranjang
if (isset($_GET['clear'])) {
    $koneksi->query("DELETE FROM cart WHERE customer_id = $customer_id");
    header("Location: keranjang.php");
    exit();
}

// Ambil data keranjang
$cart_items = $koneksi->query("SELECT c.*, p.nama_produk, p.harga, p.gambar 
                               FROM cart c 
                               JOIN products p ON c.product_id = p.id 
                               WHERE c.customer_id = $customer_id");

$total_harga = 0;
$items = [];
while ($item = $cart_items->fetch_assoc()) {
    $item['subtotal'] = $item['quantity'] * $item['harga'];
    $total_harga += $item['subtotal'];
    $items[] = $item;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Warung Makan Rizal</title>
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
        .cart-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .quantity-input {
            width: 70px;
            text-align: center;
        }
        .btn-checkout {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            border: none;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            color: white;
        }
        .btn-checkout:hover {
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
                    <a class="nav-link active" href="keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang</a>
                    <a class="nav-link" href="pesanan_saya.php"><i class="fas fa-history"></i> Pesanan Saya</a>
                    <a class="nav-link" href="profil.php"><i class="fas fa-user"></i> Profil</a>
                    <hr class="bg-secondary mx-3">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-0">
                <nav class="navbar navbar-custom px-4 py-3 text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Keranjang Belanja</h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                </nav>
                
                <div class="p-4">
                    <?php if(count($items) > 0): ?>
                        <div class="card shadow-sm">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Produk</th>
                                                <th>Harga</th>
                                                <th>Jumlah</th>
                                                <th>Subtotal</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($items as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo $item['gambar'] && file_exists("../uploads/produk/".$item['gambar']) ? '../uploads/produk/'.$item['gambar'] : 'https://placehold.co/60x60'; ?>" class="cart-img me-3">
                                                        <div>
                                                            <strong><?php echo $item['nama_produk']; ?></strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo format_rupiah($item['harga']); ?></td>
                                                <td>
                                                    <form method="POST" class="d-flex align-items-center">
                                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" class="form-control quantity-input me-2" min="1" max="99">
                                                        <button type="submit" name="update" class="btn btn-sm btn-primary">Update</button>
                                                    </form>
                                                </td>
                                                <td><?php echo format_rupiah($item['subtotal']); ?></td>
                                                <td>
                                                    <a href="?hapus=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus item ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>Total</strong></td>
                                                <td><strong><?php echo format_rupiah($total_harga); ?></strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="?clear=1" class="btn btn-outline-danger" onclick="return confirm('Kosongkan keranjang?')">
                                            <i class="fas fa-trash-alt"></i> Kosongkan Keranjang
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <a href="checkout.php" class="btn btn-checkout">
                                            <i class="fas fa-credit-card"></i> Lanjut ke Checkout
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card shadow-sm text-center py-5">
                            <div class="card-body">
                                <i class="fas fa-shopping-cart fa-4x text-muted mb-3 d-block"></i>
                                <h5>Keranjang Kosong</h5>
                                <p class="text-muted">Belum ada produk di keranjang Anda</p>
                                <a href="menu.php" class="btn" style="background: #6F4E37; color: white;">
                                    <i class="fas fa-utensils"></i> Mulai Belanja
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>