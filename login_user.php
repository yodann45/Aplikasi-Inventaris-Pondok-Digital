<?php
session_start();
include "config.php";




if(isset($_POST['login'])){
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password']));

    $q = mysqli_query($koneksi, "SELECT * FROM users WHERE username='".mysqli_real_escape_string($koneksi,$username)."' AND password='$password' LIMIT 1");
    if(mysqli_num_rows($q)==1){
        $u = mysqli_fetch_assoc($q);
        if($u['status'] !== 'approved'){
            $error = "Akun Anda belum disetujui admin. Status: " . $u['status'];
        } else {
            // set session
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['user_username'] = $u['username'];
            $_SESSION['user_name'] = $u['display_name'] ?: $u['username'];
            $_SESSION['role'] = $u['role']; // penting!
            header("Location: user_dashboard.php");
            exit;
        }
    } else {
        $error = "Username / password salah.";
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Login Tamu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: url('assets/pondok.jpg') center/cover no-repeat fixed;
      position: relative;
    }
    /* Overlay redup */
    body::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.55);
      backdrop-filter: blur(3px);
      z-index: 0;
    }

    /* Card Login */
    .login-card {
      position: relative;
      z-index: 1;
      background: rgba(255,255,255,0.9);
      backdrop-filter: blur(6px);
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.25);
      animation: fadeUp 1s ease;
    }

    .login-card .card-header {
      border-radius: 15px 15px 0 0;
      text-align: center;
      font-weight: bold;
      font-size: 1.3rem;
      background: linear-gradient(90deg, #198754, #28a745);
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    /* Input */
    .form-control {
      border-radius: 8px;
      transition: all 0.3s ease;
    }
    .form-control:focus {
      border-color: #198754;
      box-shadow: 0 0 6px rgba(25,135,84,0.5);
    }

    /* Button */
    .btn-success {
      border-radius: 30px;
      transition: all 0.3s ease;
      font-weight: 500;
    }
    .btn-success:hover {
      background: #157347;
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.25);
    }
    .btn-outline-primary {
      border-radius: 30px;
      font-weight: 500;
    }
    .btn-outline-primary:hover {
      background: #0d6efd;
      color: #fff;
    }

    /* Link button */
    .btn-link {
      font-size: 0.9rem;
      text-decoration: none;
      color: #198754;
    }
    .btn-link:hover {
      text-decoration: underline;
    }

    /* Animasi */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<div class="container" style="max-width:420px;">
  <div class="card login-card">
    <div class="card-header text-white">Login Tamu</div>
    <div class="card-body p-4">
      <?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-success w-100 mb-3" name="login">Login</button>
      </form>
      <a href="register.php" class="btn btn-outline-primary w-100 mb-2">Daftar akun baru</a>
      <a href="index.php" class="btn btn-link w-100">Kembali ke Dashboard Utama</a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
