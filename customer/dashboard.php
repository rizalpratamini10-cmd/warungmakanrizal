<?php
include '../config/database.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$customer = $koneksi->query("SELECT * FROM users WHERE id = $customer_id")->fetch_assoc();

// Statistik
$total_orders = $koneksi->query("SELECT COUNT(*) as total FROM orders WHERE customer_id = $customer_id")->fetch_assoc()['total'];
$pending_orders = $koneksi->query("SELECT COUNT(*) as total FROM orders WHERE customer_id = $customer_id AND status = 'pending'")->fetch_assoc()['total'];
$completed_orders = $koneksi->query("SELECT COUNT(*) as total FROM orders WHERE customer_id = $customer_id AND status = 'selesai'")->fetch_assoc()['total'];
$total_spent = $koneksi->query("SELECT SUM(total_harga) as total FROM orders WHERE customer_id = $customer_id AND status = 'selesai'")->fetch_assoc()['total'] ?? 0;

// Pesanan terbaru
$recent_orders = $koneksi->query("SELECT * FROM orders WHERE customer_id = $customer_id ORDER BY created_at DESC LIMIT 5");

// Notifikasi
$notifications = $koneksi->query("SELECT * FROM notifications WHERE user_id = $customer_id AND is_read = 0 ORDER BY created_at DESC LIMIT 5");
$unread_count = $notifications->num_rows;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Warung Makan Rizal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6F4E37; --secondary: #D4AF37; }
        body { background: #f5f5f5; font-family: 'Segoe UI', sans-serif; }
        
        /* Navbar Atas */
        .navbar-top {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            padding: 12px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        .navbar-top .navbar-brand {
            color: white;
            font-weight: 600;
            font-size: 20px;
        }
        .navbar-top .nav-link {
            color: white;
        }
        
        /* Sidebar Kiri */
        .dashboard-sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            width: 260px;
            height: calc(100% - 60px);
            background: #2D1F1A;
            color: white;
            overflow-y: auto;
            z-index: 999;
            transition: all 0.3s;
        }
        
        /* Profile Section in Sidebar */
        .sidebar-profile {
            text-align: center;
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-profile .avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #D4AF37;
            margin-bottom: 12px;
        }
        .sidebar-profile .username {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .sidebar-profile .email {
            font-size: 12px;
            color: rgba(255,255,255,0.7);
        }
        
        /* Navigation Menu in Sidebar */
        .sidebar-nav {
            padding: 20px 0;
        }
        .sidebar-nav .nav-item {
            list-style: none;
        }
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
        }
        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #D4AF37;
        }
        .sidebar-nav .nav-link.active {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            color: #D4AF37;
            border-right: 3px solid #D4AF37;
        }
        .sidebar-nav .nav-link i {
            width: 22px;
        }
        .sidebar-nav .nav-group-title {
            font-size: 12px;
            font-weight: 600;
            color: rgba(255,255,255,0.5);
            padding: 15px 20px 5px 20px;
            letter-spacing: 0.5px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 260px;
            margin-top: 60px;
            padding: 20px;
            min-height: calc(100vh - 60px);
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stat-card .icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .stat-card .icon.primary { background: rgba(107, 70, 55, 0.1); color: #6F4E37; }
        .stat-card .icon.warning { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
        .stat-card .icon.success { background: rgba(40, 167, 69, 0.1); color: #28a745; }
        .stat-card .icon.danger { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .stat-card .label {
            color: #666;
            font-size: 14px;
        }
        
        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            border-radius: 12px;
            padding: 25px;
            color: white;
            margin-bottom: 25px;
        }
        
        /* Table */
        .table-custom {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .table-custom th {
            background: #f8f9fa;
            padding: 12px 15px;
            font-weight: 600;
        }
        .table-custom td {
            padding: 12px 15px;
            vertical-align: middle;
        }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-pending { background: #ffc107; color: #856404; }
        .status-diproses { background: #17a2b8; color: white; }
        .status-dikirim { background: #007bff; color: white; }
        .status-selesai { background: #28a745; color: white; }
        
        /* Quick Menu */
        .quick-menu {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
            color: #333;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .quick-menu:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            color: #D4AF37;
        }
        .quick-menu i {
            font-size: 32px;
            margin-bottom: 10px;
            color: #6F4E37;
        }
        .quick-menu:hover i {
            color: #D4AF37;
        }
        .quick-menu h6 {
            margin-bottom: 5px;
        }
        
        /* Notification Dropdown */
        .notification-badge {
            position: relative;
        }
        .notification-count {
            position: absolute;
            top: -8px;
            right: -8px;
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
        
        @media (max-width: 768px) {
            .dashboard-sidebar {
                left: -260px;
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>

<!-- Navbar Atas -->
<nav class="navbar navbar-expand-lg navbar-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-utensils"></i> Warung Makan Rizal
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Notifikasi -->
                <li class="nav-item notification-badge me-3">
                    <a class="nav-link" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <?php if($unread_count > 0): ?>
                            <span class="notification-count"><?php echo $unread_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                        <h6 class="dropdown-header">Notifikasi</h6>
                        <?php if($notifications->num_rows > 0): ?>
                            <?php while($n = $notifications->fetch_assoc()): ?>
                                <a class="dropdown-item" href="#">
                                    <strong><?php echo $n['title']; ?></strong><br>
                                    <small><?php echo $n['message']; ?></small>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="dropdown-item text-muted">Tidak ada notifikasi</div>
                        <?php endif; ?>
                    </div>
                </li>
                <!-- User -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?php echo $_SESSION['customer_nama']; ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="akun_saya.php">
                            <i class="fas fa-user-cog"></i> Akun Saya
                        </a>
                        <a class="dropdown-item" href="pesanan_saya.php">
                            <i class="fas fa-receipt"></i> Pesanan Saya
                        </a>
                        <hr class="dropdown-divider">
                        <a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Dashboard Sidebar Kiri -->
<div class="dashboard-sidebar">
    <div class="sidebar-profile">
        <img src="https://ui-avatars.com/api/?background=D4AF37&color=6F4E37&name=<?php echo urlencode($customer['nama_lengkap']); ?>" class="avatar" alt="Avatar">
        <div class="username"><?php echo $customer['nama_lengkap']; ?></div>
        <div class="email"><?php echo $customer['email']; ?></div>
    </div>
    
    <div class="sidebar-nav">
        <div class="nav-group-title">MAIN NAVIGATION</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="menu.php">
                    <i class="fas fa-utensils"></i> Menu Makanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="keranjang.php">
                    <i class="fas fa-shopping-cart"></i> Keranjang
                    <?php $cart_count = get_cart_count($customer_id); if($cart_count > 0): ?>
                        <span class="badge bg-danger ms-2"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
        
        <div class="nav-group-title">AKUN SAYA</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="akun_saya.php?menu=profil">
                    <i class="fas fa-user"></i> Profil
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="akun_saya.php?menu=alamat">
                    <i class="fas fa-map-marker-alt"></i> Alamat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="akun_saya.php?menu=password">
                    <i class="fas fa-key"></i> Ubah Password
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="akun_saya.php?menu=notifikasi">
                    <i class="fas fa-bell"></i> Notifikasi
                </a>
            </li>
        </ul>
        
        <div class="nav-group-title">PESANAN</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="pesanan_saya.php">
                    <i class="fas fa-history"></i> Riwayat Pesanan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-ticket-alt"></i> Voucher Saya
                </a>
            </li>
        </ul>
        
        <div class="nav-group-title">LAINNYA</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-home"></i> Beranda
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Selamat datang, <?php echo $_SESSION['customer_nama']; ?>! 👋</h4>
                <p class="mb-0 opacity-75">Yuk pesan makanan favorit Anda hari ini</p>
            </div>
            <div>
                <a href="menu.php" class="btn btn-light" style="color: #6F4E37;">
                    <i class="fas fa-utensils"></i> Pesan Sekarang
                </a>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="value"><?php echo $total_orders; ?></div>
                    <div class="label">Total Pesanan</div>
                </div>
                <div class="icon primary">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="value text-warning"><?php echo $pending_orders; ?></div>
                    <div class="label">Diproses</div>
                </div>
                <div class="icon warning">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="value text-success"><?php echo $completed_orders; ?></div>
                    <div class="label">Selesai</div>
                </div>
                <div class="icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card d-flex justify-content-between align-items-center">
                <div>
                    <div class="value"><?php echo format_rupiah($total_spent); ?></div>
                    <div class="label">Total Belanja</div>
                </div>
                <div class="icon danger">
                    <i class="fas fa-money-bill"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Pesanan Terbaru -->
    <div class="table-custom mb-4">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <h5 class="mb-0"><i class="fas fa-history me-2" style="color: #6F4E37;"></i> Pesanan Terbaru</h5>
            <a href="pesanan_saya.php" class="btn btn-sm" style="background: #6F4E37; color: white;">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($recent_orders->num_rows > 0): ?>
                        <?php while($order = $recent_orders->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $order['order_number']; ?></strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                            <td><?php echo format_rupiah($order['total_harga']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="detail_pesanan.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-2 d-block"></i>
                                <p>Belum ada pesanan</p>
                                <a href="menu.php" class="btn btn-sm" style="background: #6F4E37; color: white;">Mulai Pesan</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Quick Menu -->
    <div class="row g-3">
        <div class="col-md-4">
            <a href="menu.php" class="quick-menu">
                <i class="fas fa-utensils"></i>
                <h6>Lihat Menu</h6>
                <small class="text-muted">Pesan makanan favorit</small>
            </a>
        </div>
        <div class="col-md-4">
            <a href="keranjang.php" class="quick-menu">
                <i class="fas fa-shopping-cart"></i>
                <h6>Keranjang</h6>
                <small class="text-muted">Lihat pesanan Anda</small>
            </a>
        </div>
        <div class="col-md-4">
            <a href="akun_saya.php" class="quick-menu">
                <i class="fas fa-user-cog"></i>
                <h6>Pengaturan</h6>
                <small class="text-muted">Kelola akun Anda</small>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>