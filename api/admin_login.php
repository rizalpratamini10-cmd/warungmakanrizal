<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$username = mysqli_real_escape_string($koneksi, $data['username']);
$password = $data['password'];

$query = "SELECT * FROM users WHERE (username = '$username' OR email = '$username') AND role = 'admin' AND is_active = 1";
$result = $koneksi->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $token = bin2hex(random_bytes(32));
        echo json_encode([
            'success' => true,
            'token' => $token,
            'admin' => [
                'id' => $user['id'],
                'nama_lengkap' => $user['nama_lengkap'],
                'email' => $user['email']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Password salah']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Akun tidak ditemukan']);
}
?>