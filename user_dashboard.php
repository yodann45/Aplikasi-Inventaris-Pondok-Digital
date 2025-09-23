<?php
session_start();
include "config.php";

// Cek login
if(!isset($_SESSION['user_id'])){
    header("Location: login_user.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id=$user_id"));

if (!empty($user['foto']) && file_exists("uploads/" . $user['foto'])) {
    $foto = "uploads/" . $user['foto'];
} else {
    $foto = "assets/img/default.jpeg";
}

$q1 = mysqli_query($koneksi, "SELECT COUNT(*) as total 
    FROM peminjaman 
    WHERE user_id=$user_id AND status='diterima'");
$jml_dipinjam = mysqli_fetch_assoc($q1)['total'];

$q2 = mysqli_query($koneksi, "SELECT COUNT(*) as total 
    FROM peminjaman 
    WHERE user_id=$user_id AND status='pending'");
$jml_pending = mysqli_fetch_assoc($q2)['total'];

$q3 = mysqli_query($koneksi, "SELECT COUNT(*) as total 
    FROM peminjaman 
    WHERE user_id=$user_id AND status IN ('selesai','ditolak')");
$jml_riwayat = mysqli_fetch_assoc($q3)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Tamu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #e9f7ef, #d4edda);
      min-height: 100vh;
    }
    .card-custom {
      border: none;
      border-radius: 20px;
      transition: transform .25s ease, box-shadow .25s ease;
    }
    .card-custom:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 28px rgba(0,0,0,0.2);
    }
        /* Ganti style profile-img */


    .btn-lg {
      border-radius: 12px;
      transition: all .2s ease;
      padding: 12px 20px;
      font-size: 1.1rem;
    }
    .btn-lg:hover {
      transform: scale(1.05);
    }
  </style>
</head>
<body>

<!-- Navbar -->
<?php include 'navbar.php'; ?>

<div class="container mt-3 pt-5">
    <div class="text-center mb-3">
  <h2 class="fw-bold">Halo, <?= htmlspecialchars($user['display_name'] ?? $user['username']); ?> 👋</h2>
  <p class="text-muted">Selamat datang di sistem peminjaman barang pondok.</p>
    </div>



  <div class="row text-center g-4">
    <div class="col-md-4">
      <div class="card card-custom shadow-sm" style="background:#17a2b8;color:white;">
        <div class="card-body py-4">
          <h2 class="fw-bold"><?= $jml_dipinjam; ?></h2>
          <p class="mb-0">Sedang Dipinjam</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-custom shadow-sm" style="background:#ffc107;color:black;">
        <div class="card-body py-4">
          <h2 class="fw-bold"><?= $jml_pending; ?></h2>
          <p class="mb-0">Menunggu Persetujuan</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-custom shadow-sm" style="background:#28a745;color:white;">
        <div class="card-body py-4">
          <h2 class="fw-bold"><?= $jml_riwayat; ?></h2>
          <p class="mb-0">Total Riwayat</p>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-5 text-center">
    <h4 class="fw-semibold">Apa yang ingin Anda lakukan?</h4>
    <div class="d-flex flex-wrap gap-3 justify-content-center mt-3">
      <a href="peminjaman.php" class="btn btn-primary btn-lg shadow-sm">📦 Pinjam Barang</a>
      <a href="pengembalian.php" class="btn btn-success btn-lg shadow-sm">↩️ Kembalikan Barang</a>
      <a href="riwayat.php" class="btn btn-secondary btn-lg shadow-sm">📜 Lihat Riwayat</a>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
