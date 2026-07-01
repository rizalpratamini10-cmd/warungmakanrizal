<?php
include '../config/database.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$admin_nama = $_SESSION['admin_nama'];

// Statistik (sama seperti dashboard biasa tapi tanpa manajemen admin)
$total_orders = $koneksi->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$pending_orders = $koneksi->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")->fetch_assoc()['total'];
$total_revenue = $koneksi->query("SELECT SUM(total_harga) as total FROM orders WHERE status = 'selesai'")->fetch_assoc()['total'] ?? 0;
$total_customers = $koneksi->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'")->fetch_assoc()['total'];
$total_products = $koneksi->query("SELECT COUNT(*) as total FROM products WHERE is_available = 1")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Warung Makan Rizal</title>
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
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            color: #D4AF37;
        }
        .sidebar .nav-link i { width: 25px; margin-right: 8px; }
        .navbar-custom {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
        }
        .card-stats {
            border: none;
            border-radius: 15px;
            background: white;
            transition: all 0.3s;
        }
        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .status-pending { background: #ffc107; color: #856404; }
        .status-diproses { background: #17a2b8; color: white; }
        .status-dikirim { background: #007bff; color: white; }
        .status-selesai { background: #28a745; color: white; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="text-center py-4 border-bottom border-secondary">
                    <i class="fas fa-store fa-3x" style="color: #D4AF37;"></i>
                    <h5 class="mt-2">Warung Rizal</h5>
                    <small>Admin Panel (<?php echo $_SESSION['admin_role']; ?>)</small>
                </div>
                <nav class="nav flex-column mt-3">
                    <a class="nav-link active" href="dashboard_admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a class="nav-link" href="pesanan.php"><i class="fas fa-shopping-cart"></i> Pesanan</a>
                    <a class="nav-link" href="produk.php"><i class="fas fa-utensils"></i> Produk</a>
                    <a class="nav-link" href="kategori.php"><i class="fas fa-tags"></i> Kategori</a>
                    <a class="nav-link" href="customer.php"><i class="fas fa-users"></i> Customer</a>
                    <a class="nav-link" href="laporan.php"><i class="fas fa-chart-line"></i> Laporan</a>
                    <hr class="bg-secondary mx-3">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-0">
                <nav class="navbar navbar-custom px-4 py-3 text-white">
                    <h5 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin</h5>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x me-2"></i>
                        <span><?php echo $admin_nama; ?> (Admin)</span>
                    </div>
                </nav>
                
                <div class="p-4">
                    <div class="alert alert-light border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #6F4E37, #A0522D); color: white;">
                        <h4 class="mb-1">Selamat datang, <?php echo $admin_nama; ?>! 👋</h4>
                        <p class="mb-0">Kelola toko dengan mudah di sini</p>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div><h6 class="text-muted mb-1">Total Pesanan</h6><h2 class="mb-0"><?php echo $total_orders; ?></h2></div>
                                        <i class="fas fa-shopping-bag fa-3x text-muted"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div><h6 class="text-muted mb-1">Pesanan Pending</h6><h2 class="mb-0 text-warning"><?php echo $pending_orders; ?></h2></div>
                                        <i class="fas fa-clock fa-3x text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div><h6 class="text-muted mb-1">Total Pendapatan</h6><h5 class="mb-0"><?php echo format_rupiah($total_revenue); ?></h5></div>
                                        <i class="fas fa-money-bill fa-3x text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div><h6 class="text-muted mb-1">Customer</h6><h2 class="mb-0"><?php echo $total_customers; ?></h2></div>
                                        <i class="fas fa-users fa-3x text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div><h6 class="text-muted mb-1">Total Produk</h6><h2 class="mb-0"><?php echo $total_products; ?></h2></div>
                                        <i class="fas fa-utensils fa-3x text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card text-center shadow-sm p-3">
                                <i class="fas fa-chart-line fa-3x mb-2" style="color: #6F4E37;"></i>
                                <h6>Laporan Penjualan</h6>
                                <a href="laporan.php" class="btn btn-sm" style="background: #6F4E37; color: white;">Lihat Laporan</a>
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