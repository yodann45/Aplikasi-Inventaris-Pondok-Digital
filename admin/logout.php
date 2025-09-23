<?php
session_start();

// Hapus semua session admin
session_unset();
session_destroy();

// Redirect ke dashboard tamu (halaman utama)
header("Location: ../index.php");
exit;
?>
