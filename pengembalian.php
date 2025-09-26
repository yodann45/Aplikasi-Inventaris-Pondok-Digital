<?php
include "config.php";
session_start();
if(!isset($_SESSION['user_id'])){ 
    header("Location: login_user.php"); 
    exit; 
}

$user_id = (int)$_SESSION['user_id'];

// handle pengembalian via POST
if(isset($_POST['confirm_return'])){
    $peminjaman_id = (int)$_POST['peminjaman_id'];
    $tgl = $_POST['tgl_pengembalian'];   // date (Y-m-d)
    $jam = $_POST['jam_pengembalian'];   // time (H:i)

    // gabungkan jadi timestamp string
    $tgl_pengembalian = $tgl . " " . $jam . ":00";

    $sql = "INSERT INTO pengembalian (peminjaman_id, tgl_pengembalian, status) 
            VALUES ($peminjaman_id, '$tgl_pengembalian', 'pending')";
    mysqli_query($koneksi, $sql);

    $msg = "Permintaan pengembalian berhasil diajukan, menunggu persetujuan admin.";
}

// Pagination
$limit = 6;
$page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
$offset = ($page-1)*$limit;

$total_q = mysqli_query($koneksi, "
    SELECT COUNT(*) as total 
    FROM peminjaman p
    WHERE p.user_id=$user_id AND p.status='diterima'
");
$total = mysqli_fetch_assoc($total_q)['total'];
$pages = ceil($total/$limit);

// Ambil list peminjaman user (beserta status pengembalian jika ada)
$list = mysqli_query($koneksi, "
    SELECT p.id, p.jumlah, p.tanggal_pinjam, p.rencana_kembali, b.nama_barang,
           pg.status as return_status, pg.tgl_pengembalian
    FROM peminjaman p
    JOIN barang b ON p.barang_id=b.id
    LEFT JOIN pengembalian pg ON pg.peminjaman_id=p.id
    WHERE p.user_id=$user_id 
      AND p.status='diterima'
    ORDER BY p.id DESC
    LIMIT $limit OFFSET $offset
");
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Pengembalian Saya</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .pinjam-card {
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }
    .pinjam-card:hover { transform: translateY(-5px); }
    .card-title { font-weight: 600; color: #198754; }
  </style>
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>

<div class="container mt-4">
  <h3 class="mb-4">Pengembalian Saya</h3>
  <?php if(!empty($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>

  <div class="row g-4">
    <?php $no=$offset+1; while($r = mysqli_fetch_assoc($list)): ?>
      <div class="col-md-4">
        <div class="card pinjam-card h-100">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?= htmlspecialchars($r['nama_barang']); ?></h5>
            <ul class="list-unstyled mb-3 text-muted small">
              <li><strong>Jumlah:</strong> <?= $r['jumlah']; ?></li>
              <li><strong>Tgl Pinjam:</strong> <?= $r['tanggal_pinjam']; ?></li>
              <li><strong>Rencana Kembali:</strong> <?= $r['rencana_kembali']; ?></li>
            </ul>
            <div class="mt-auto">
              <?php if($r['return_status']): ?>
                <?php if($r['return_status']=='pending'): ?>
                  <span class="badge bg-warning w-100">Menunggu Persetujuan Admin</span>
                <?php elseif($r['return_status']=='diterima'): ?>
                  <span class="badge bg-success w-100">Pengembalian Diterima</span>
                <?php elseif($r['return_status']=='ditolak'): ?>
                  <span class="badge bg-danger w-100">Pengembalian Ditolak</span>
                <?php endif; ?>
              <?php else: ?>
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#returnModal<?= $r['id']; ?>">
                  Ajukan Pengembalian
                </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Konfirmasi -->
      <div class="modal fade" id="returnModal<?= $r['id']; ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <form method="post">
              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Konfirmasi Pengembalian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <p>Anda akan mengajukan pengembalian untuk 
                  <strong><?= htmlspecialchars($r['nama_barang']); ?></strong> (<?= $r['jumlah']; ?> unit).
                </p>
                <div class="mb-3">
                  <label class="form-label">Tanggal Pengembalian</label>
                  <input type="date" name="tgl_pengembalian" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Jam Pengembalian</label>
                  <input type="time" name="jam_pengembalian" class="form-control" required>
                </div>
                <input type="hidden" name="peminjaman_id" value="<?= $r['id']; ?>">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="confirm_return" class="btn btn-primary">Ya, Ajukan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    <?php endwhile; ?>

    <?php if($total==0): ?>
      <div class="col-12">
        <div class="alert alert-info text-center">Tidak ada data pengembalian tersedia</div>
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
