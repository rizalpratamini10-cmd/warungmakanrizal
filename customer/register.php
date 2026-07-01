<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Cek email sudah terdaftar
    $check = $koneksi->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $error = "Email sudah terdaftar!";
    } else {
        $query = "INSERT INTO users (username, password, nama_lengkap, email, no_hp, alamat, role) 
                  VALUES ('$email', '$password', '$nama', '$email', '$no_hp', '$alamat', 'customer')";
        
        if ($koneksi->query($query)) {
            echo "<script>alert('Pendaftaran berhasil! Silakan login.'); location.href='login.php';</script>";
            exit();
        } else {
            $error = "Gagal mendaftar: " . $koneksi->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Warung Makan Rizal</title>
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
            padding: 40px 0;
        }
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 550px;
            margin: auto;
        }
        .register-header {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .register-header i {
            font-size: 50px;
            color: #D4AF37;
        }
        .register-body {
            padding: 40px;
        }
        .btn-register {
            background: linear-gradient(135deg, #6F4E37, #A0522D);
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            color: white;
        }
        .btn-register:hover {
            background: linear-gradient(135deg, #A0522D, #6F4E37);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-card">
            <div class="register-header">
                <i class="fas fa-user-plus"></i>
                <h3 class="mt-3">Daftar Akun</h3>
                <p class="mb-0">Customer Warung Makan Rizal</p>
            </div>
            <div class="register-body">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>
                    <button type="submit" class="btn btn-register">
                        <i class="fas fa-user-check"></i> Daftar
                    </button>
                </form>
                <hr>
                <p class="text-center mb-0">
                    Sudah punya akun? <a href="login.php" style="color: #6F4E37;">Login</a>
                </p>
                <p class="text-center mt-2">
                    <a href="../index.php" style="color: #6F4E37;">← Kembali ke Beranda</a>
                </p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>