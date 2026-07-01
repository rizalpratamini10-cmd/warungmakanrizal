<?php
include 'config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Aplikasi - Warung Makan Rizal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #D2691E;
            --secondary: #FF8C00;
        }
        body {
            background: linear-gradient(135deg, #f5f5f5 0%, #fff 100%);
            font-family: 'Segoe UI', sans-serif;
        }
        .hero {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 80px 0;
            color: white;
            text-align: center;
        }
        .app-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            height: 100%;
        }
        .app-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .app-icon {
            width: 100px;
            height: 100px;
            border-radius: 25px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            color: white;
        }
        .icon-customer { background: linear-gradient(135deg, #28a745, #20c997); }
        .icon-kurir { background: linear-gradient(135deg, #007bff, #00c6ff); }
        .icon-admin { background: linear-gradient(135deg, #dc3545, #ff6b6b); }
        .btn-download {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s;
        }
        .btn-download:hover {
            transform: scale(1.05);
            color: white;
        }
        .qr-code {
            width: 150px;
            height: 150px;
            margin: 15px auto;
            background: #f0f0f0;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .version-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
        }
        .feature-list {
            text-align: left;
            margin-top: 20px;
        }
        .feature-list li {
            margin-bottom: 8px;
            font-size: 13px;
        }
        footer {
            background: #2C1810;
            color: white;
            padding: 40px 0;
            margin-top: 60px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #D2691E, #FF8C00);">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-utensils"></i> Warung Makan Rizal
        </a>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1 class="mb-3"><i class="fas fa-download"></i> Download Aplikasi</h1>
        <p class="lead">Pilih aplikasi sesuai kebutuhan Anda</p>
    </div>
</section>

<!-- App Cards -->
<div class="container mt-5">
    <div class="row g-4">
        
        <!-- Aplikasi Customer -->
        <div class="col-md-4">
            <div class="app-card position-relative">
                <span class="version-badge">v1.0.0</span>
                <div class="app-icon icon-customer">
                    <i class="fas fa-user"></i>
                </div>
                <h3>Warung Rizal</h3>
                <p class="text-muted">Aplikasi untuk Pembeli</p>
                <div class="qr-code">
                    <i class="fas fa-qrcode fa-4x text-muted"></i>
                </div>
                <ul class="feature-list">
                    <li><i class="fas fa-check-circle text-success"></i> Pesan makanan online</li>
                    <li><i class="fas fa-check-circle text-success"></i> Tracking pesanan realtime</li>
                    <li><i class="fas fa-check-circle text-success"></i> Riwayat pesanan</li>
                    <li><i class="fas fa-check-circle text-success"></i> Notifikasi push</li>
                    <li><i class="fas fa-check-circle text-success"></i> Rating & testimoni</li>
                </ul>
                <a href="downloads/WarungRizal-Customer.apk" class="btn btn-download w-100" download>
                    <i class="fas fa-download"></i> Download APK (Customer)
                </a>
                <p class="mt-2 small text-muted">Ukuran: ~8 MB | Android 5.0+</p>
            </div>
        </div>
        
        <!-- Aplikasi Kurir -->
        <div class="col-md-4">
            <div class="app-card position-relative">
                <span class="version-badge">v1.0.0</span>
                <div class="app-icon icon-kurir">
                    <i class="fas fa-motorcycle"></i>
                </div>
                <h3>Warung Rizal Kurir</h3>
                <p class="text-muted">Aplikasi untuk Driver</p>
                <div class="qr-code">
                    <i class="fas fa-qrcode fa-4x text-muted"></i>
                </div>
                <ul class="feature-list">
                    <li><i class="fas fa-check-circle text-success"></i> Ambil pesanan</li>
                    <li><i class="fas fa-check-circle text-success"></i> Tracking GPS realtime</li>
                    <li><i class="fas fa-check-circle text-success"></i> Hitung jarak & upah</li>
                    <li><i class="fas fa-check-circle text-success"></i> Riwayat pendapatan</li>
                    <li><i class="fas fa-check-circle text-success"></i> Navigasi ke customer</li>
                </ul>
                <a href="downloads/WarungRizal-Kurir.apk" class="btn btn-download w-100" download>
                    <i class="fas fa-download"></i> Download APK (Kurir)
                </a>
                <p class="mt-2 small text-muted">Ukuran: ~10 MB | Android 5.0+</p>
            </div>
        </div>
        
        <!-- Aplikasi Admin -->
        <div class="col-md-4">
            <div class="app-card position-relative">
                <span class="version-badge">v1.0.0</span>
                <div class="app-icon icon-admin">
                    <i class="fas fa-store"></i>
                </div>
                <h3>Warung Rizal Admin</h3>
                <p class="text-muted">Aplikasi untuk Owner</p>
                <div class="qr-code">
                    <i class="fas fa-qrcode fa-4x text-muted"></i>
                </div>
                <ul class="feature-list">
                    <li><i class="fas fa-check-circle text-success"></i> Dashboard realtime</li>
                    <li><i class="fas fa-check-circle text-success"></i> Kelola produk & menu</li>
                    <li><i class="fas fa-check-circle text-success"></i> Kelola pesanan</li>
                    <li><i class="fas fa-check-circle text-success"></i> Kelola kurir</li>
                    <li><i class="fas fa-check-circle text-success"></i> Laporan penjualan</li>
                </ul>
                <a href="downloads/WarungRizal-Admin.apk" class="btn btn-download w-100" download>
                    <i class="fas fa-download"></i> Download APK (Admin)
                </a>
                <p class="mt-2 small text-muted">Ukuran: ~9 MB | Android 5.0+</p>
            </div>
        </div>
        
    </div>
    
    <!-- Cara Install -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4><i class="fas fa-info-circle text-primary"></i> Cara Install APK</h4>
                    <ol class="mt-3">
                        <li>Download APK sesuai kebutuhan Anda</li>
                        <li>Buka file APK yang sudah di download</li>
                        <li>Izinkan instalasi dari sumber tidak dikenal (Settings → Security → Unknown Sources)</li>
                        <li>Klik Install dan tunggu hingga selesai</li>
                        <li>Buka aplikasi dan login dengan akun Anda</li>
                    </ol>
                    <div class="alert alert-warning">
                        <i class="fas fa-shield-alt"></i> <strong>Keamanan:</strong> Pastikan Anda mendownload dari website resmi ini.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="container text-center">
        <h5>Warung Makan Rizal</h5>
        <p>Masakan Rumahan Lezat, Harga Bersahabat</p>
        <hr class="bg-light">
        <p>&copy; 2024 Warung Makan Rizal | All Rights Reserved</p>
    </div>
</footer>

</body>
</html>