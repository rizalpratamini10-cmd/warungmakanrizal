<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include '../config/database.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = mysqli_real_escape_string($koneksi, $data['email']);
$password = $data['password'];

$query = "SELECT * FROM users WHERE email = '$email' AND role = 'customer' AND is_active = 1";
$result = $koneksi->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $token = bin2hex(random_bytes(32));
        echo json_encode([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'nama_lengkap' => $user['nama_lengkap'],
                'email' => $user['email'],
                'no_hp' => $user['no_hp'],
                'alamat' => $user['alamat']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Password salah']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Email tidak terdaftar']);
}
?>