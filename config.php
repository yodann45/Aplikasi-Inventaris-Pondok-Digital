<?php
$host = "localhost";
$user = "root";   // default phpMyAdmin
$pass = "";       // kosongkan kalau tidak pakai password
$db   = "inventori_pondok";  // ← ganti di sini

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
