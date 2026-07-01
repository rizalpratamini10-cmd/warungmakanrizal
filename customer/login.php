<?php
include '../config/database.php';

if (isset($_SESSION['customer_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE email = '$email' AND role = 'customer' AND is_active = 1";
    $result = $koneksi->query($query);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['customer_id'] = $user['id'];
            $_SESSION['customer_nama'] = $user['nama_lengkap'];
            $_SESSION['customer_email'] = $user['email'];
            $_SESSION['customer_no_hp'] = $user['no_hp'];
            $_SESSION['customer_alamat'] = $user['alamat'];
            
            // Redirect ke halaman sebelumnya jika ada
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard.php';
            header("Location: $redirect");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak terdaftar!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Customer - Warung Makan Rizal</title>
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
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 450px;
            margin: auto;
        }
        .login-header {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-header i {
            font-size: 50px;
            color: #D4AF37;
        }
        .login-body {
            padding: 40px;
        }
        .btn-login {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            color: white;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #A0522D, #6F4E37);
            color: white;
        }
        .btn-register {
            background: transparent;
            border: 2px solid #6F4E37;
            border-radius: 50px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            color: #6F4E37;
        }
        .btn-register:hover {
            background: #6F4E37;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-utensils"></i>
                <h3 class="mt-3">Warung Makan Rizal</h3>
                <p class="mb-0">Login Customer</p>
            </div>
            <div class="login-body">
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                    <button type="submit" class="btn btn-login mb-3">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                    <hr>
                    <a href="register.php" class="btn btn-register">
                        <i class="fas fa-user-plus"></i> Daftar Akun Baru
                    </a>
                </form>
                
                <!-- ============ TAMBAHKAN INI ============ -->
                <hr>
                <p class="text-center mb-0">
                    <a href="../pilih_login.php" style="color: #6F4E37;">
                        <i class="fas fa-arrow-left"></i> Pilih Role Lain
                    </a>
                </p>
                <!-- ====================================== -->
                
                <hr>
                <p class="text-center mb-0">
                    <a href="../index.php" style="color: #6F4E37;">← Kembali ke Beranda</a>
                </p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>