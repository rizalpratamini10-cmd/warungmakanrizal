<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Login - Warung Makan Rizal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #6F4E37; --secondary: #D4AF37; }
        body {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .role-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            height: 100%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .role-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .role-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
        }
        .role-icon.customer { background: linear-gradient(135deg, #28a745, #20c997); }
        .role-icon.kurir { background: linear-gradient(135deg, #007bff, #00c6ff); }
        .role-icon.admin { background: linear-gradient(135deg, #dc3545, #ff6b6b); }
        .role-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .role-desc {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .btn-role {
            background: #6F4E37;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            width: 100%;
        }
        .btn-role:hover {
            background: #A0522D;
            color: white;
        }
        .back-link {
            text-align: center;
            margin-top: 30px;
        }
        .back-link a {
            color: white;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-4">
            <i class="fas fa-utensils fa-4x" style="color: #D4AF37;"></i>
            <h2 class="text-white mt-2">Warung Makan Rizal</h2>
            <p class="text-white">Pilih role untuk login</p>
        </div>
        
        <div class="row g-4">
            <!-- Customer -->
            <div class="col-md-4">
                <div class="role-card" onclick="window.location.href='customer/login.php'">
                    <div class="role-icon customer">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="role-title">Customer</div>
                    <div class="role-desc">Saya ingin memesan makanan</div>
                    <button class="btn-role">
                        <i class="fas fa-sign-in-alt"></i> Login sebagai Customer
                    </button>
                </div>
            </div>
            
            <!-- Kurir -->
            <div class="col-md-4">
                <div class="role-card" onclick="window.location.href='kurir/login.php'">
                    <div class="role-icon kurir">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <div class="role-title">Kurir</div>
                    <div class="role-desc">Saya kurir yang mengantar pesanan</div>
                    <button class="btn-role">
                        <i class="fas fa-sign-in-alt"></i> Login sebagai Kurir
                    </button>
                </div>
            </div>
            
            <!-- Admin -->
            <div class="col-md-4">
                <div class="role-card" onclick="window.location.href='admin/login.php'">
                    <div class="role-icon admin">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="role-title">Admin</div>
                    <div class="role-desc">Saya pemilik / pengelola toko</div>
                    <button class="btn-role">
                        <i class="fas fa-sign-in-alt"></i> Login sebagai Admin
                    </button>
                </div>
            </div>
        </div>
        
        <div class="back-link">
            <a href="index.php">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>