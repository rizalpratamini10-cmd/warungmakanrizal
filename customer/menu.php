<?php
include '../config/database.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Filter
$kategori = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

$query = "SELECT p.*, c.nama_kategori 
          FROM products p 
          LEFT JOIN categories c ON p.kategori_id = c.id 
          WHERE p.is_available = 1";

if ($kategori > 0) {
    $query .= " AND p.kategori_id = $kategori";
}
if ($search) {
    $query .= " AND p.nama_produk LIKE '%$search%'";
}
$query .= " ORDER BY p.nama_produk ASC";

$products = $koneksi->query($query);
$categories = $koneksi->query("SELECT * FROM categories ORDER BY nama_kategori");

// Ambil jumlah keranjang
$cart_count = get_cart_count($customer_id);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Warung Makan Rizal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6F4E37; --secondary: #D4AF37; }
        body { background: #f5f5f5; font-family: 'Segoe UI', sans-serif; }
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
        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            transition: all 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .product-img {
            height: 180px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-img i {
            font-size: 4rem;
            color: #D4AF37;
        }
        .product-info {
            padding: 15px;
        }
        .product-name {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .product-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #6F4E37;
            margin-bottom: 10px;
        }
        .btn-add-cart {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            border: none;
            border-radius: 50px;
            padding: 8px;
            width: 100%;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-add-cart:hover {
            background: linear-gradient(135deg, #A0522D, #6F4E37);
            transform: scale(1.02);
        }
        .cart-badge {
            position: relative;
        }
        .cart-count {
            position: absolute;
            top: -8px;
            right: -12px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
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
                    <a class="nav-link active" href="menu.php"><i class="fas fa-utensils"></i> Menu Makanan</a>
                    <a class="nav-link cart-badge" href="keranjang.php">
                        <i class="fas fa-shopping-cart"></i> Keranjang
                        <?php if($cart_count > 0): ?>
                            <span class="cart-count"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <a class="nav-link" href="pesanan_saya.php"><i class="fas fa-history"></i> Pesanan Saya</a>
                    <a class="nav-link" href="profil.php"><i class="fas fa-user"></i> Profil</a>
                    <hr class="bg-secondary mx-3">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-0">
                <nav class="navbar navbar-custom px-4 py-3 text-white">
                    <h5 class="mb-0"><i class="fas fa-utensils me-2"></i>Menu Makanan</h5>
                    <div class="d-flex align-items-center">
                        <a href="keranjang.php" class="btn btn-sm position-relative me-3" style="background: #D4AF37; color: #3E2723;">
                            <i class="fas fa-shopping-cart"></i> Keranjang
                            <?php if($cart_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $cart_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <i class="fas fa-user-circle fa-2x"></i>
                    </div>
                </nav>
                
                <div class="p-4">
                    <!-- Search & Filter -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Cari menu..." value="<?php echo $search; ?>">
                                        <button type="submit" class="btn" style="background: #6F4E37; color: white;">
                                            <i class="fas fa-search"></i> Cari
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <select name="kategori" class="form-select" onchange="this.form.submit()">
                                        <option value="0">Semua Kategori</option>
                                        <?php while($cat = $categories->fetch_assoc()): ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php echo $kategori == $cat['id'] ? 'selected' : ''; ?>>
                                                <?php echo $cat['nama_kategori']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <?php if($search || $kategori > 0): ?>
                                <div class="col-md-2">
                                    <a href="menu.php" class="btn btn-secondary w-100">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Products Grid -->
                    <div class="row">
                        <?php if($products->num_rows > 0): ?>
                            <?php while($p = $products->fetch_assoc()): ?>
                            <div class="col-lg-3 col-md-4 col-6">
                                <div class="product-card">
                                    <div class="product-img">
                                        <?php if($p['gambar'] && file_exists("../uploads/produk/".$p['gambar'])): ?>
                                            <img src="../uploads/produk/<?php echo $p['gambar']; ?>" alt="<?php echo $p['nama_produk']; ?>">
                                        <?php else: ?>
                                            <i class="fas fa-hamburger"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-name"><?php echo $p['nama_produk']; ?></div>
                                        <div class="product-price"><?php echo format_rupiah($p['harga']); ?></div>
                                        <button class="btn-add-cart" onclick="addToCart(<?php echo $p['id']; ?>)">
                                            <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-search fa-4x text-muted mb-3 d-block"></i>
                                <h5>Menu tidak ditemukan</h5>
                                <p class="text-muted">Coba kata kunci lain atau lihat kategori lainnya</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast Notification -->
    <div id="toast" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;"></div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function addToCart(productId) {
            $.ajax({
                url: '../api/add_to_cart.php',
                method: 'POST',
                data: { product_id: productId, quantity: 1 },
                success: function(response) {
                    var data = JSON.parse(response);
                    var toast = $('#toast');
                    toast.html('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-check-circle"></i> ' + data.message +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                    setTimeout(function() { toast.html(''); }, 2000);
                    // Update cart count
                    setTimeout(function() { location.reload(); }, 1500);
                },
                error: function() {
                    alert('Gagal menambahkan ke keranjang');
                }
            });
        }
    </script>
</body>
</html>