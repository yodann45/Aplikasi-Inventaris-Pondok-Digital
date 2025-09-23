<?php
include "config.php";
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login_user.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$u = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id=$user_id"));

// handle submit
if(isset($_POST['pinjam'])){
    $barang = (int) $_POST['barang_id'];
    $jumlah = (int) $_POST['jumlah'];
    $tanggal_pinjam = $_POST['tanggal_pinjam'];
    $pickup_time = $_POST['pickup_time'];
    $rencana_kembali = $_POST['tgl_kembali'];
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);

    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT stok FROM barang WHERE id=$barang"));
    if($jumlah > $cek['stok']){
        $msg = "<div class='alert alert-danger'>Jumlah melebihi stok tersedia!</div>";
    } else {
        $sql = "INSERT INTO peminjaman 
                (user_id, barang_id, jumlah, tanggal_pinjam, pickup_time, rencana_kembali, keterangan, status)
                VALUES ($user_id, $barang, $jumlah, '$tanggal_pinjam', '$pickup_time', '$rencana_kembali', '$keterangan', 'pending')";
        mysqli_query($koneksi, $sql);

        $msg = "<div class='alert alert-success'>Permintaan peminjaman berhasil dikirim! Tunggu persetujuan admin.</div>";
    }
}

$barang_q = mysqli_query($koneksi, "SELECT * FROM barang WHERE status='aktif' AND stok > 0");
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Form Peminjaman</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }
    .form-card {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
      padding: 2rem;
      margin-top: 2rem;
    }
    h2 {
      font-weight: bold;
      color: #198754;
    }
    .form-control, .form-select {
      border-radius: 10px;
    }
    .btn-success {
      border-radius: 30px;
      padding: 0.6rem 1.2rem;
      font-weight: 500;
    }
    .modal-content {
      border-radius: 15px;
      overflow: hidden;
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
  <div class="form-card">
    <h2 class="mb-4">Form Peminjaman</h2>
    <?php if(!empty($msg)) echo $msg; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label fw-semibold">Nama</label>
        <input class="form-control" value="<?= htmlspecialchars($u['display_name'] ?: $u['username']) ?>" disabled>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Alamat</label>
        <textarea class="form-control" disabled><?= htmlspecialchars($u['alamat'] ?? '') ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Nomor Telepon</label>
        <input class="form-control" value="<?= htmlspecialchars($u['phone'] ?? '') ?>" disabled>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Pilih Barang</label>
        <select name="barang_id" id="barangSelect" class="form-select" required>
          <option value="">--Pilih Barang--</option>
          <?php while($b = mysqli_fetch_assoc($barang_q)): ?>
            <option value="<?= $b['id'] ?>" data-stok="<?= $b['stok'] ?>">
              <?= htmlspecialchars($b['nama_barang']) ?> (Stok: <?= $b['stok'] ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label fw-semibold">Jumlah</label>
          <select name="jumlah" id="jumlahSelect" class="form-select" required>
            <option value="">--Pilih Jumlah--</option>
          </select>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label fw-semibold">Tanggal Pinjam</label>
          <input type="date" name="tanggal_pinjam" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label fw-semibold">Jam Pengambilan</label>
          <input type="time" name="pickup_time" class="form-control" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Rencana Tanggal Kembali</label>
        <input type="date" name="tgl_kembali" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold">Keterangan</label>
        <textarea name="keterangan" class="form-control"></textarea>
      </div>

      <!-- Tombol panggil modal konfirmasi -->
      <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmPinjam">
        Kirim Permintaan
      </button>

      <!-- Modal konfirmasi -->
      <div class="modal fade" id="confirmPinjam" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-success text-white">
              <h5 class="modal-title">Konfirmasi Peminjaman</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <p>Apakah Anda yakin ingin mengirim permintaan peminjaman ini?</p>
              <small class="text-muted">Setelah dikirim, admin akan memproses permintaan Anda.</small>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" name="pinjam" class="btn btn-success">Ya, Kirim</button>
            </div>
          </div>
        </div>
      </div>

    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('barangSelect').addEventListener('change', function(){
  const stok = this.options[this.selectedIndex].getAttribute('data-stok');
  const jumlahSelect = document.getElementById('jumlahSelect');

  jumlahSelect.innerHTML = '<option value="">--Pilih Jumlah--</option>';

  if(stok){
    for(let i=1; i<=stok; i++){
      let opt = document.createElement('option');
      opt.value = i;
      opt.textContent = i;
      jumlahSelect.appendChild(opt);
    }
  }
});
</script>
</body>
</html>
