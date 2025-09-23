<?php
session_start();
include "../config.php";
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

// --- Konfigurasi pagination ---
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$total_q = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peminjaman");
$total_data = mysqli_fetch_assoc($total_q)['total'];
$total_pages = ceil($total_data / $limit);

// Aksi terima
if(isset($_GET['terima'])){
    $id = intval($_GET['terima']);
    $q = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id=$id");
    $data = mysqli_fetch_assoc($q);

    if($data){
        mysqli_query($koneksi, "UPDATE barang SET stok = stok - {$data['jumlah']} WHERE id={$data['barang_id']}");
        mysqli_query($koneksi, "UPDATE peminjaman SET status='diterima' WHERE id=$id");
    }

    header("Location: peminjaman.php?page=$page");
    exit;
}

// Aksi tolak
if(isset($_GET['tolak'])){
    $id = intval($_GET['tolak']);
    mysqli_query($koneksi, "UPDATE peminjaman SET status='ditolak' WHERE id=$id");
    header("Location: peminjaman.php?page=$page");
    exit;
}

// Ambil data dengan LIMIT
$peminjaman = mysqli_query($koneksi, "
    SELECT p.*, b.nama_barang, u.display_name, u.username, u.alamat, u.phone
    FROM peminjaman p
    JOIN barang b ON p.barang_id = b.id
    JOIN users u ON p.user_id = u.id
    ORDER BY p.id DESC
    LIMIT $limit OFFSET $offset
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Peminjaman</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'navbar_admin.php'; ?>

<div class="container mt-4">
  <h2>Data Peminjaman</h2>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-success">
        <tr>
          <th>No</th>
          <th>Nama Peminjam</th>
          <th>Alamat</th>
          <th>Barang</th>
          <th>Jumlah</th>
          <th>Tgl Pinjam</th>
          <th>Tgl Kembali</th>
          <th>Jam Ambil</th>
          <th>Keterangan</th>
          <th>Telepon</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=$offset+1; while($row=mysqli_fetch_assoc($peminjaman)){ ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= htmlspecialchars($row['display_name'] ?: $row['username']); ?>
<td><?= htmlspecialchars($row['alamat'] ?? '-'); ?></td>
<td><?= htmlspecialchars($row['nama_barang']); ?></td>
<td><?= htmlspecialchars($row['jumlah']); ?></td>
<td><?= htmlspecialchars($row['tanggal_pinjam']); ?></td>
<td><?= htmlspecialchars($row['rencana_kembali']); ?></td>
<td><?= htmlspecialchars($row['pickup_time']); ?></td>
<td><?= htmlspecialchars($row['keterangan']); ?></td>
<td>
  <?php if(!empty($row['phone'])): ?>
    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $row['phone']); ?>" target="_blank" class="btn btn-sm btn-outline-success">💬 WA</a>
    <br><small><?= htmlspecialchars($row['phone']); ?></small>
  <?php else: ?>
    <span class="text-muted">-</span>
  <?php endif; ?>
</td>

          <td>
            <?php if($row['status']=='pending'){ ?>
              <span class="badge bg-warning">Menunggu</span>
            <?php } elseif($row['status']=='diterima'){ ?>
              <span class="badge bg-success">Diterima</span>
            <?php } elseif($row['status']=='selesai'){ ?>
              <span class="badge bg-info">Selesai</span>
            <?php } else { ?>
              <span class="badge bg-danger">Ditolak</span>
            <?php } ?>
          </td>
          <td>
            <?php if($row['status']=='pending'){ ?>
              <a href="peminjaman.php?terima=<?= $row['id']; ?>&page=<?= $page ?>" class="btn btn-success btn-sm">Terima</a>
              <a href="peminjaman.php?tolak=<?= $row['id']; ?>&page=<?= $page ?>" class="btn btn-danger btn-sm">Tolak</a>
            <?php } else { echo "-"; } ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <nav>
    <ul class="pagination justify-content-center">
      <?php if($page > 1): ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?= $page-1; ?>">Sebelumnya</a>
        </li>
      <?php endif; ?>

      <?php for($i=1; $i<=$total_pages; $i++): ?>
        <li class="page-item <?= ($i==$page)?'active':''; ?>">
          <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
        </li>
      <?php endfor; ?>

      <?php if($page < $total_pages): ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?= $page+1; ?>">Berikutnya</a>
        </li>
      <?php endif; ?>
    </ul>
  </nav>

</div>
</body>
</html>
