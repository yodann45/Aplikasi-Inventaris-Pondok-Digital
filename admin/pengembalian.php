<?php
session_start();
include "../config.php";
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

// Aksi terima pengembalian
if(isset($_GET['terima'])){
    $id = (int) $_GET['terima'];

    // Ambil data pengembalian + peminjaman
    $q = mysqli_query($koneksi, "
        SELECT pg.*, pm.barang_id, pm.jumlah, pm.id as peminjaman_id
        FROM pengembalian pg
        JOIN peminjaman pm ON pg.peminjaman_id = pm.id
        WHERE pg.id=$id
    ");
    $data = mysqli_fetch_assoc($q);

    if($data){
        // Tambahkan stok kembali
        mysqli_query($koneksi, "UPDATE barang SET stok = stok + {$data['jumlah']} WHERE id={$data['barang_id']}");
        // Update status pengembalian
        mysqli_query($koneksi, "UPDATE pengembalian SET status='diterima' WHERE id=$id");
        // Update status peminjaman jadi selesai
        mysqli_query($koneksi, "UPDATE peminjaman SET status='selesai' WHERE id={$data['peminjaman_id']}");
    }

    header("Location: pengembalian.php?page=".(isset($_GET['page']) ? $_GET['page'] : 1));
    exit;
}

// Aksi tolak
if(isset($_GET['tolak'])){
    $id = (int) $_GET['tolak'];
    mysqli_query($koneksi, "UPDATE pengembalian SET status='ditolak' WHERE id=$id");
    header("Location: pengembalian.php?page=".(isset($_GET['page']) ? $_GET['page'] : 1));
    exit;
}

// --- Pagination ---
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$total_result = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengembalian");
$total_pengembalian = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_pengembalian / $limit);

// Ambil data sesuai halaman
$pengembalian = mysqli_query($koneksi, "
    SELECT pg.*, u.display_name AS nama_peminjam, b.nama_barang, pm.jumlah 
    FROM pengembalian pg
    JOIN peminjaman pm ON pg.peminjaman_id = pm.id
    JOIN users u ON pm.user_id = u.id
    JOIN barang b ON pm.barang_id = b.id
    ORDER BY pg.id DESC
    LIMIT $limit OFFSET $offset
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Pengembalian</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar_admin.php'; ?>

<div class="container mt-4">
  <h2>Data Pengembalian</h2>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>No</th>
          <th>Nama Peminjam</th>
          <th>Barang</th>
          <th>Jumlah</th>
          <th>Tgl Pengembalian</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $no = $offset + 1;
        while($row=mysqli_fetch_assoc($pengembalian)){ ?>
        <tr>
          <td><?= $no++; ?></td>
          <td><?= htmlspecialchars($row['nama_peminjam']); ?></td>
          <td><?= htmlspecialchars($row['nama_barang']); ?></td>
          <td><?= htmlspecialchars($row['jumlah']); ?></td>
          <td><?= htmlspecialchars($row['tgl_pengembalian']); ?></td>
          <td>
            <?php if($row['status']=='pending'){ ?>
              <span class="badge bg-warning">Menunggu</span>
            <?php } elseif($row['status']=='diterima'){ ?>
              <span class="badge bg-success">Diterima</span>
            <?php } else { ?>
              <span class="badge bg-danger">Ditolak</span>
            <?php } ?>
          </td>
          <td>
            <?php if($row['status']=='pending'){ ?>
              <a href="pengembalian.php?terima=<?= $row['id']; ?>&page=<?= $page; ?>" class="btn btn-success btn-sm" onclick="return confirm('Terima pengembalian ini?')">Terima</a>
              <a href="pengembalian.php?tolak=<?= $row['id']; ?>&page=<?= $page; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tolak pengembalian ini?')">Tolak</a>
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
      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page-1; ?>">Previous</a>
      </li>
      <?php for($i=1; $i <= $total_pages; $i++){ ?>
        <li class="page-item <?= ($i==$page)?'active':'' ?>">
          <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
        </li>
      <?php } ?>
      <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
        <a class="page-link" href="?page=<?= $page+1; ?>">Next</a>
      </li>
    </ul>
  </nav>
</div>
</body>
</html>
