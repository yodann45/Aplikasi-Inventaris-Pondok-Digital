<?php
include "config.php";
session_start();

if(isset($_POST['register'])){
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password']));
    $display  = trim($_POST['display_name']);
    $phone    = trim($_POST['phone']);

    $q = mysqli_query($koneksi, "SELECT id FROM users WHERE username='".mysqli_real_escape_string($koneksi,$username)."' LIMIT 1");
    if(mysqli_num_rows($q)>0){
        $error = "Username sudah terdaftar.";
    } else {
        $sql = "INSERT INTO users (username, password, role, status, display_name, phone) 
                VALUES ('".mysqli_real_escape_string($koneksi,$username)."', '$password', 'tamu', 'pending', '".mysqli_real_escape_string($koneksi,$display)."', '".mysqli_real_escape_string($koneksi,$phone)."')";
        if(mysqli_query($koneksi, $sql)){
            $success = true;
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Register - Tamu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      background: url('assets/pondok.jpg') center/cover no-repeat fixed;
    }
    .overlay {
      background: rgba(0,0,0,0.55);
      position: fixed;
      top:0; left:0;
      width:100%; height:100%;
    }
    .card {
      background: rgba(255,255,255,0.92);
      border-radius: 15px;
      transition: transform .2s ease, box-shadow .2s ease;
    }
    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    }
    .card-header {
      border-radius: 15px 15px 0 0 !important;
    }
    .form-control:focus {
      border-color: #198754;
      box-shadow: 0 0 0 0.2rem rgba(25,135,84,.25);
    }
    .btn-success {
      border-radius: 8px;
      transition: all .2s ease;
    }
    .btn-success:hover {
      background-color: #157347;
      transform: scale(1.02);
    }
  </style>
</head>
<body>
<div class="overlay d-flex align-items-center justify-content-center">
  <div class="container" style="max-width:600px;">
    <div class="card shadow-lg">
      <div class="card-header bg-success text-white text-center fs-5 fw-bold">
        Registrasi Akun Tamu
      </div>
      <div class="card-body p-4">
        <?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input class="form-control" name="username" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Tampilan</label>
            <input class="form-control" name="display_name">
            <div class="form-text">Boleh berbeda dari username</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Nomor Telepon</label>
            <input class="form-control" name="phone">
          </div>
          <button class="btn btn-success w-100 py-2" name="register">Daftar</button>
        </form>
        <hr>
        <div class="text-center">
          <a href="login_user.php" class="btn btn-link">Sudah punya akun? Login</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if(!empty($success)): ?>
<!-- Modal sukses -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Registrasi Berhasil</h5>
      </div>
      <div class="modal-body">
        Akun Anda berhasil dibuat.<br>
        Silakan tunggu persetujuan admin sebelum bisa login.
      </div>
      <div class="modal-footer">
        <a href="login_user.php" class="btn btn-success">Lanjut ke Login</a>
      </div>
    </div>
  </div>
</div>
<script>
  var myModal = new bootstrap.Modal(document.getElementById('successModal'));
  myModal.show();
  setTimeout(function(){
    window.location.href = "login_user.php";
  }, 3000);
</script>
<?php endif; ?>
</body>
</html>
