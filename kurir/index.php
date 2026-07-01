<?php
session_start();
if (isset($_SESSION['kurir_id'])) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit();
?>