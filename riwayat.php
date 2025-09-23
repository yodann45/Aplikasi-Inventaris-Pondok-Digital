<?php
include "config.php";
session_start();
if(!isset($_SESSION['user_id'])){ 
    header("Location: login_user.php"); 
    exit; 
}
$user_id = (int)$_SESSION['user_id'];

// Pagination
$limit = 6;
$page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
$offset = ($page-1)*$limit;

// total rows
$total_q = mysqli_query($koneksi, "
    SELECT COUNT(*) as total 
    FROM peminjaman 
    WHERE user_id=$user_id
");
$total = mysqli_fetch_assoc($total_q)['total'];
$pages = ceil($total/$limit);

// fetch data
$q = mysqli_query($koneksi, "
  SELECT p.*, b.nama_barang, pg.id as return_id, pg.tgl_pengembalian, pg.status as return_status
  FROM peminjaman p
  JOIN barang b ON p.barang_id = b.id
  LEFT JOIN pengembalian pg ON pg.peminjaman_id = p.id
  WHERE p.user_id = $user_id
  ORDER BY p.id DESC
  LIMIT $limit OFFSET $offset
");
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Riwayat Saya</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .riwayat-card {
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .riwayat-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }
    .badge { font-size: 0.85rem; }
  </style>
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container mt-4">
  <h3 class="mb-4">Riwayat Peminjaman & Pengembalian</h3>

  <div class="row g-4">
    <?php $no=$offset+1; while($r=mysqli_fetch_assoc($q)): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card riwayat-card h-100">
          <div class="card-body">
            <h5 class="card-title text-success"><?= htmlspecialchars($r['nama_barang']); ?></h5>
            <p class="mb-1"><strong>Jumlah:</strong> <?= $r['jumlah']; ?></p>
            <p class="mb-1">
              <strong>Tgl Pinjam:</strong> <?= $r['tanggal_pinjam']; ?>
              <?php if(!empty($r['pickup_time'])): ?>
                <br><small class="text-muted">(<?= $r['pickup_time']; ?>)</small>
              <?php endif; ?>
            </p>
            <p class="mb-1"><strong>Tgl Rencana Kembali:</strong> <?= $r['rencana_kembali']; ?></p>

            <!-- Status Peminjaman -->
            <p class="mb-1">
              <strong>Status Peminjaman:</strong><br>
              <?php 
                if($r['status']=='pending'){ 
                    echo "<span class='badge bg-warning'>Pending</span>"; 
                } elseif($r['status']=='diterima'){ 
                    echo "<span class='badge bg-success'>Diterima</span>"; 
                } elseif($r['status']=='ditolak'){ 
                    echo "<span class='badge bg-danger'>Ditolak</span>"; 
                } elseif($r['status']=='selesai'){ 
                    echo "<span class='badge bg-primary'>Selesai</span>"; 
                } else { 
                    echo "<span class='badge bg-secondary'>-</span>"; 
                }
              ?>
            </p>

            <!-- Status Pengembalian -->
            <p class="mb-0">
              <strong>Status Pengembalian:</strong><br>
              <?php 
                if($r['return_id']) {
                    if($r['return_status']=='pending') echo "<span class='badge bg-warning'>Menunggu Verif</span>";
                    elseif($r['return_status']=='diterima') echo "<span class='badge bg-success'>Diterima</span>";
                    elseif($r['return_status']=='ditolak') echo "<span class='badge bg-danger'>Ditolak</span>";
                } else {
                    echo "<span class='text-muted'>Belum diajukan</span>";
                }
              ?>
            </p>
          </div>
        </div>
      </div>
    <?php endwhile; ?>

    <?php if($total==0): ?>
      <div class="col-12 text-center">
        <div class="alert alert-info">Belum ada riwayat peminjaman</div>
      </div>
    <?php endif; ?>
  </div>

  <!-- pagination -->
  <nav class="mt-4">
    <ul class="pagination justify-content-center">
      <?php for($p=1;$p<=$pages;$p++): ?>
        <li class="page-item <?= $p==$page?'active':'' ?>">
          <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
