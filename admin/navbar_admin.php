<?php
// Hitung pending
$notif_users = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM users WHERE status='pending'"))['jml'];
$notif_pinjam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM peminjaman WHERE status='pending'"))['jml'];
$notif_kembali = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pengembalian WHERE status='pending'"))['jml'];
$total_notif = $notif_users + $notif_pinjam + $notif_kembali;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="barang.php">Barang</a></li>
        <li class="nav-item"><a class="nav-link" href="peminjaman.php">Peminjaman 
          <?php if($notif_pinjam>0) echo "<span class='badge bg-danger'>$notif_pinjam</span>"; ?></a></li>
        <li class="nav-item"><a class="nav-link" href="pengembalian.php">Pengembalian 
          <?php if($notif_kembali>0) echo "<span class='badge bg-danger'>$notif_kembali</span>"; ?></a></li>
        <li class="nav-item"><a class="nav-link" href="approve_users.php">User 
          <?php if($notif_users>0) echo "<span class='badge bg-danger'>$notif_users</span>"; ?></a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
    <?php if($total_notif>0){ ?>
      <span class="badge bg-warning text-dark ms-3">🔔 <?= $total_notif; ?></span>
    <?php } ?>
  </div>
</nav>
