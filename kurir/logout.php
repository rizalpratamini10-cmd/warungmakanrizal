<?php
session_start();
include '../config/database.php';

if (isset($_SESSION['kurir_id'])) {
    $kurir_id = $_SESSION['kurir_id'];
    // Update status offline
    $koneksi->query("UPDATE kurir_locations SET is_online = 0, last_update = NOW() WHERE kurir_id = $kurir_id");
    session_destroy();
}
header("Location: login.php");
exit();
?>