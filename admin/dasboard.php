<?php
include '../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah super admin
$is_super_admin = ($_SESSION['admin_role'] == 'super_admin');
$admin_nama = $_SESSION['admin_nama'];

// Statistik
$total_orders = $koneksi->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$pending_orders = $koneksi->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'")->fetch_assoc()['total'];
$total_revenue = $koneksi->query("SELECT SUM(total_harga) as total FROM orders WHERE status = 'selesai'")->fetch_assoc()['total'] ?? 0;
$total_customers = $koneksi->query("SELECT COUNT(*) as total FROM users WHERE role = 'customer'")->fetch_assoc()['total'];
$total_products = $koneksi->query("SELECT COUNT(*) as total FROM products WHERE is_available = 1")->fetch_assoc()['total'];
$total_kurir = $koneksi->query("SELECT COUNT(*) as total FROM users WHERE role = 'kurir'")->fetch_assoc()['total'];
$online_kurir = $koneksi->query("SELECT COUNT(DISTINCT kurir_id) as online FROM kurir_locations WHERE is_online = 1 AND TIMESTAMPDIFF(MINUTE, last_update, NOW()) < 5")->fetch_assoc()['online'] ?? 0;

// Pesanan terbaru
$recent_orders = $koneksi->query("SELECT o.*, u.nama_lengkap as customer_name FROM orders o JOIN users u ON o.customer_id = u.id ORDER BY o.created_at DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Super Admin - Warung Makan Rizal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6F4E37; --secondary: #D4AF37; --dark: #3E2723; }
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
        .sidebar .nav-link i {
            width: 25px;
            margin-right: 8px;
        }
        .card-stats {
            border: none;
            border-radius: 15px;
            background: white;
            transition: all 0.3s;
            cursor: pointer;
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
        .navbar-custom {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
        }
        .badge-super {
            background: #D4AF37;
            color: #3E2723;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 20px;
            margin-left: 8px;
        }
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
                    <small>Super Admin Panel</small>
                </div>
                <nav class="nav flex-column mt-3">
                    <a class="nav-link active" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="pesanan.php">
                        <i class="fas fa-shopping-cart"></i> Pesanan
                        <?php if($pending_orders > 0): ?>
                            <span class="badge bg-danger float-end"><?php echo $pending_orders; ?></span>
                        <?php endif; ?>
                    </a>
                    <a class="nav-link" href="produk.php">
                        <i class="fas fa-utensils"></i> Produk
                    </a>
                    <a class="nav-link" href="kategori.php">
                        <i class="fas fa-tags"></i> Kategori
                    </a>
                    <a class="nav-link" href="customer.php">
                        <i class="fas fa-users"></i> Customer
                    </a>
                    <a class="nav-link" href="kurir.php">
                        <i class="fas fa-motorcycle"></i> Kurir
                    </a>
                    <!-- Menu khusus Super Admin -->
                    <a class="nav-link" href="manage_admin.php">
                        <i class="fas fa-user-shield"></i> Kelola Admin
                    </a>
                    <a class="nav-link" href="laporan.php">
                        <i class="fas fa-chart-line"></i> Laporan
                    </a>
                    <a class="nav-link" href="profil.php">
                        <i class="fas fa-user-cog"></i> Profil
                    </a>
                    <hr class="bg-secondary mx-3">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-0">
                <!-- Navbar -->
                <nav class="navbar navbar-custom px-4 py-3 text-white">
                    <div>
                        <h5 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Super Admin</h5>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle fa-2x me-2"></i>
                        <span><?php echo $admin_nama; ?> <span class="badge-super">Super Admin</span></span>
                    </div>
                </nav>
                
                <div class="p-4">
                    <!-- Welcome Card -->
                    <div class="alert alert-light border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #6F4E37, #A0522D); color: white;">
                        <h4 class="mb-1">Selamat datang, <?php echo $admin_nama; ?>! 👑</h4>
                        <p class="mb-0">Anda memiliki akses penuh sebagai Super Admin</p>
                    </div>
                    
                    <!-- Statistik Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Pesanan</h6>
                                            <h2 class="mb-0"><?php echo $total_orders; ?></h2>
                                        </div>
                                        <div class="rounded-circle bg-warning bg-opacity-25 p-3">
                                            <i class="fas fa-shopping-cart fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Pesanan Pending</h6>
                                            <h2 class="mb-0 text-warning"><?php echo $pending_orders; ?></h2>
                                        </div>
                                        <div class="rounded-circle bg-danger bg-opacity-25 p-3">
                                            <i class="fas fa-clock fa-2x text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Pendapatan</h6>
                                            <h5 class="mb-0"><?php echo format_rupiah($total_revenue); ?></h5>
                                        </div>
                                        <div class="rounded-circle bg-success bg-opacity-25 p-3">
                                            <i class="fas fa-money-bill fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Customer</h6>
                                            <h2 class="mb-0"><?php echo $total_customers; ?></h2>
                                        </div>
                                        <div class="rounded-circle bg-info bg-opacity-25 p-3">
                                            <i class="fas fa-users fa-2x text-info"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Baris Statistik 2 -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Produk</h6>
                                            <h2 class="mb-0"><?php echo $total_products; ?></h2>
                                        </div>
                                        <div class="rounded-circle bg-primary bg-opacity-25 p-3">
                                            <i class="fas fa-utensils fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Kurir</h6>
                                            <h2 class="mb-0"><?php echo $total_kurir; ?></h2>
                                        </div>
                                        <div class="rounded-circle bg-secondary bg-opacity-25 p-3">
                                            <i class="fas fa-motorcycle fa-2x text-secondary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Kurir Online</h6>
                                            <h2 class="mb-0 text-success"><?php echo $online_kurir; ?></h2>
                                        </div>
                                        <div class="rounded-circle bg-success bg-opacity-25 p-3">
                                            <i class="fas fa-wifi fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Rata-rata per Pesanan</h6>
                                            <h5 class="mb-0"><?php echo $total_orders > 0 ? format_rupiah($total_revenue / $total_orders) : format_rupiah(0); ?></h5>
                                        </div>
                                        <div class="rounded-circle bg-dark bg-opacity-25 p-3">
                                            <i class="fas fa-chart-line fa-2x text-dark"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pesanan Terbaru -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-history me-2" style="color: #6F4E37;"></i>Pesanan Terbaru</h5>
                            <a href="pesanan.php" class="btn btn-sm" style="background: #6F4E37; color: white;">Lihat Semua</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Pesanan</th>
                                            <th>Customer</th>
                                            <th>Tanggal</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($order = $recent_orders->fetch_assoc()): ?>
                                        <tr>
                                            <td><strong>#<?php echo $order['order_number']; ?></strong></td>
                                            <td><?php echo $order['customer_name']; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                            <td><?php echo format_rupiah($order['total_harga']); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="pesanan.php?detail=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Menu Cepat -->
                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <i class="fas fa-plus-circle fa-3x mb-2" style="color: #6F4E37;"></i>
                                    <h6>Tambah Produk Baru</h6>
                                    <a href="produk.php?action=tambah" class="btn btn-sm" style="background: #6F4E37; color: white;">Tambah</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <i class="fas fa-user-plus fa-3x mb-2" style="color: #6F4E37;"></i>
                                    <h6>Tambah Admin</h6>
                                    <a href="manage_admin.php" class="btn btn-sm" style="background: #6F4E37; color: white;">Tambah</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center shadow-sm">
                                <div class="card-body">
                                    <i class="fas fa-chart-line fa-3x mb-2" style="color: #6F4E37;"></i>
                                    <h6>Lihat Laporan</h6>
                                    <a href="laporan.php" class="btn btn-sm" style="background: #6F4E37; color: white;">Lihat</a>
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