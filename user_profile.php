<?php
session_start();
include "config.php";

// Cek login
if(!isset($_SESSION['user_id'])){
    header("Location: login_user.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Ambil data user
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id=$user_id"));

// Proses update profil
if(isset($_POST['update'])){
    $nama   = mysqli_real_escape_string($koneksi, $_POST['display_name']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $phone  = mysqli_real_escape_string($koneksi, $_POST['phone']);
    $gender = !empty($_POST['gender']) ? "'".mysqli_real_escape_string($koneksi, $_POST['gender'])."'" : "NULL";

    // Upload foto
    if(!empty($_FILES['foto']['name'])){
        $foto = time().'_'.basename($_FILES['foto']['name']);
        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/".$foto);
        $foto_sql = "'".$foto."'";
    } else {
        $foto_sql = "foto"; // tetap pakai foto lama kalau tidak upload
    }

    $sql = "UPDATE users 
            SET display_name='$nama',
                alamat='$alamat',
                phone='$phone',
                gender=$gender,
                foto=$foto_sql
            WHERE id=$user_id";

    mysqli_query($koneksi, $sql);

    // Refresh data user
    $user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id=$user_id"));

    $success = "Profil berhasil diperbarui!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Profil Saya</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }
    .profile-card {
      max-width: 650px;
      margin: 40px auto;
      border-radius: 15px;
      overflow: hidden;
    }
    .profile-header {
      background: linear-gradient(135deg, #0d6efd, #198754);
      color: white;
      padding: 30px 20px;
      text-align: center;
    }
    .profile-img {
      width: 130px;
      height: 130px;
      object-fit: cover;
      border: 4px solid #fff;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .form-control, .form-select {
      border-radius: 10px;
      padding: 12px;
    }
    .btn {
      border-radius: 10px;
      padding: 10px 20px;
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
  <div class="card shadow profile-card">
    <div class="profile-header">
     
      <h4 class="mb-0">Profil Saya</h4>
      <small><?= htmlspecialchars($user['email'] ?? ''); ?></small>
    </div>
    <div class="card-body p-4">
      <?php if(!empty($success)): ?>
        <div class="alert alert-success"><?= $success; ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="display_name" class="form-control" 
                 value="<?= htmlspecialchars($user['display_name'] ?? ''); ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Alamat</label>
          <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Nomor HP</label>
          <input type="text" name="phone" class="form-control" 
                 value="<?= htmlspecialchars($user['phone'] ?? ''); ?>">
        </div>
        <div class="mb-3">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-select">
            <option value="" <?= empty($user['gender']) ? 'selected' : ''; ?>>Pilih...</option>
            <option value="Laki-laki" <?= ($user['gender'] ?? '')=='Laki-laki'?'selected':''; ?>>Laki-laki</option>
            <option value="Perempuan" <?= ($user['gender'] ?? '')=='Perempuan'?'selected':''; ?>>Perempuan</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Foto Profil</label>
          <input type="file" name="foto" class="form-control">
        </div>
        <div class="d-flex justify-content-between">
          <a href="user_dashboard.php" class="btn btn-secondary">Kembali</a>
          <button type="submit" name="update" class="btn btn-success">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>
