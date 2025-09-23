<?php
session_start();
include "../config.php";
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

// Tambah barang
if(isset($_POST['tambah'])){
    $nama = $_POST['nama_barang'];
    $stok = $_POST['stok'];
    $ket  = $_POST['keterangan'];

    mysqli_query($koneksi, "INSERT INTO barang (nama_barang, stok, keterangan, status) 
                            VALUES ('$nama','$stok','$ket','aktif')");
    header("Location: barang.php");
    exit;
}

// Edit barang
if(isset($_POST['edit'])){
    $id    = $_POST['id'];
    $nama  = $_POST['nama_barang'];
    $stok  = $_POST['stok'];
    $ket   = $_POST['keterangan'];
    $status = $_POST['status'];

    mysqli_query($koneksi, "UPDATE barang 
                            SET nama_barang='$nama', stok='$stok', keterangan='$ket', status='$status' 
                            WHERE id=$id");
    header("Location: barang.php");
    exit;
}

// Hapus permanen (opsional, hati-hati dengan relasi foreign key)
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM barang WHERE id=$id");
    header("Location: barang.php");
    exit;
}

$barang = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Barang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar_admin.php'; ?>

<div class="container mt-4">
  <h2>Data Barang</h2>

  <!-- Form Tambah Barang -->
  <form method="post" class="row g-3 mb-4">
    <div class="col-md-3">
      <input type="text" name="nama_barang" class="form-control" placeholder="Nama Barang" required>
    </div>
    <div class="col-md-2">
      <input type="number" name="stok" class="form-control" placeholder="Stok" required>
    </div>
    <div class="col-md-4">
      <input type="text" name="keterangan" class="form-control" placeholder="Keterangan">
    </div>
    <div class="col-md-2">
      <button type="submit" name="tambah" class="btn btn-success w-100">Tambah</button>
    </div>
  </form>

  <!-- Tabel Barang -->
  <table class="table table-bordered table-striped">
    <thead class="table-success">
      <tr>
        <th>No</th>
        <th>Nama Barang</th>
        <th>Stok</th>
        <th>Keterangan</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php $no=1; while($row=mysqli_fetch_assoc($barang)){ ?>
      <tr>
        <td><?= $no++; ?></td>
        <td><?= $row['nama_barang']; ?></td>
        <td><?= $row['stok']; ?></td>
        <td><?= $row['keterangan']; ?></td>
        <td>
          <?php if($row['status']=='aktif'){ ?>
            <span class="badge bg-success">Aktif</span>
          <?php } else { ?>
            <span class="badge bg-danger">Nonaktif</span>
          <?php } ?>
        </td>
        <td>
          <!-- Tombol Edit -->
          <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id']; ?>">Edit</button>


      <!-- Modal Edit -->
      <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form method="post">
              <div class="modal-header">
                <h5 class="modal-title">Edit Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                <div class="mb-3">
                  <label>Nama Barang</label>
                  <input type="text" name="nama_barang" class="form-control" value="<?= $row['nama_barang']; ?>" required>
                </div>
                <div class="mb-3">
                  <label>Stok</label>
                  <input type="number" name="stok" class="form-control" value="<?= $row['stok']; ?>" required>
                </div>
                <div class="mb-3">
                  <label>Keterangan</label>
                  <input type="text" name="keterangan" class="form-control" value="<?= $row['keterangan']; ?>">
                </div>
                <div class="mb-3">
                  <label>Status</label>
                  <select name="status" class="form-control">
                    <option value="aktif" <?= ($row['status']=='aktif')?'selected':''; ?>>Aktif</option>
                    <option value="nonaktif" <?= ($row['status']=='nonaktif')?'selected':''; ?>>Nonaktif</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <?php } ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
