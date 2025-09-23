<?php
session_start();
include "../config.php";
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

// Hitung ringkasan
$jml_barang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang"))['total'];
$jml_peminjaman = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman"))['total'];
$jml_pengembalian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengembalian"))['total'];

// Ambil notifikasi pending
$notif_peminjaman = mysqli_query($koneksi, "
    SELECT p.id, u.display_name, p.tanggal_pinjam 
    FROM peminjaman p 
    JOIN users u ON p.user_id=u.id 
    WHERE p.status='pending'
");
$notif_pengembalian = mysqli_query($koneksi, "
    SELECT pg.id, u.display_name, pg.tgl_pengembalian 
    FROM pengembalian pg
    JOIN peminjaman p ON pg.peminjaman_id=p.id
    JOIN users u ON p.user_id=u.id
    WHERE pg.status='pending'
");
$jml_notif = mysqli_num_rows($notif_peminjaman) + mysqli_num_rows($notif_pengembalian);

// Ambil riwayat terakhir (gabungan pinjam & kembali)
$riwayat = mysqli_query($koneksi, "
    SELECT p.id, b.nama_barang, u.display_name, u.alamat, p.jumlah, 
           p.tanggal_pinjam, p.status,
           pg.tgl_pengembalian
    FROM peminjaman p
    JOIN barang b ON p.barang_id = b.id
    JOIN users u ON p.user_id = u.id
    LEFT JOIN pengembalian pg ON pg.peminjaman_id = p.id
    ORDER BY p.id DESC LIMIT 10
");


?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f5f6fa; }
    .card-summary { transition: transform .2s; }
    .card-summary:hover { transform: scale(1.05); }
    .notif-badge {
      position: absolute;
      top: 8px;
      right: 10px;
      font-size: 12px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<?php include 'navbar_admin.php'; ?>

<!-- Ringkasan -->
<div class="container mt-4">
  <h2 class="mb-4">Dashboard Admin</h2>
  <div class="row g-3 text-center">
    <div class="col-md-4">
      <div class="card card-summary bg-info text-white">
        <div class="card-body">
          <h4><?= $jml_barang; ?></h4>
          <p>Total Barang</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-summary bg-warning text-dark">
        <div class="card-body">
          <h4><?= $jml_peminjaman; ?></h4>
          <p>Total Peminjaman</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-summary bg-success text-white">
        <div class="card-body">
          <h4><?= $jml_pengembalian; ?></h4>
          <p>Total Pengembalian</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Riwayat -->
  <div class="card mt-5">
    <div class="card-header bg-secondary text-white">📑 Riwayat Terbaru</div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Nama Peminjam</th>
            <th>Alamat</th>
            <th>Barang</th>
            <th>Jumlah</th>
            <th>Tanggal Pinjam</th>
            <th>Tanggal Kembali</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php $no=1; while($row=mysqli_fetch_assoc($riwayat)): ?>
            <tr>
              <td><?= $no++; ?></td>
              <td><?= htmlspecialchars($row['display_name']); ?></td>
              <td><?= htmlspecialchars($row['alamat']); ?></td>
              <td><?= htmlspecialchars($row['nama_barang']); ?></td>
              <td><?= $row['jumlah']; ?></td>
              <td><?= $row['tanggal_pinjam']; ?></td>
              <td><?= $row['tgl_pengembalian'] ? $row['tgl_pengembalian'] : '<span class="text-muted">Belum Kembali</span>'; ?></td>
              <td>
                <?php if($row['status']=='pending'): ?>
                  <span class="badge bg-warning">Pending</span>
                <?php elseif($row['status']=='diterima'): ?>
                  <span class="badge bg-success">Diterima</span>
                <?php elseif($row['status']=='ditolak'): ?>
                  <span class="badge bg-danger">Ditolak</span>
                <?php elseif($row['status']=='selesai'): ?>
                  <span class="badge bg-info">Selesai</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Notifikasi -->
<div class="modal fade" id="notifModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">🔔 Notifikasi Permintaan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <?php if($jml_notif==0): ?>
          <p class="text-center text-muted">Tidak ada notifikasi baru</p>
        <?php else: ?>
          <ul class="list-group">
            <?php while($p=mysqli_fetch_assoc($notif_peminjaman)): ?>
              <li class="list-group-item">
                <b><?= htmlspecialchars($p['display_name']); ?></b> mengajukan peminjaman pada <?= $p['tanggal_pinjam']; ?>
                <a href="peminjaman.php" class="btn btn-sm btn-primary float-end">Lihat</a>
              </li>
            <?php endwhile; ?>
            <?php while($k=mysqli_fetch_assoc($notif_pengembalian)): ?>
              <li class="list-group-item">
                <b><?= htmlspecialchars($k['display_name']); ?></b> mengajukan pengembalian pada <?= $k['tgl_pengembalian']; ?>
                <a href="pengembalian.php" class="btn btn-sm btn-primary float-end">Lihat</a>
              </li>
            <?php endwhile; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
