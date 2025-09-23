<?php
$user_id = (int)$_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id=$user_id"));

if (!empty($user['foto']) && file_exists("uploads/" . $user['foto'])) {
    $foto = "uploads/" . $user['foto'];
} else {
    $foto = "assets/img/default.jpeg";
}
?>

<style>
/* Navbar Custom */
.custom-navbar {
  background: linear-gradient(90deg, #198754, #28a745);
  padding: 0.8rem 0;
}

/* Brand */
.custom-navbar .navbar-brand {
  letter-spacing: 1px;
  transition: color 0.3s ease;
}
.custom-navbar .navbar-brand:hover {
  color: #d4edda;
}

/* Nav links */
.custom-navbar .nav-link {
  font-size: 1rem;
  font-weight: 500;
  padding: 8px 15px;
  border-radius: 20px;
  transition: all 0.3s ease;
}
.custom-navbar .nav-link:hover,
.custom-navbar .nav-link.active {
  background: rgba(255,255,255,0.2);
  color: #fff;
}

/* Toggler */
.navbar-toggler {
  border: none;
  outline: none;
}
.navbar-toggler:focus {
  box-shadow: none;
}

/* Foto Profil */
.profile-img {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #fff;
}
</style>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark shadow custom-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold fs-4" href="user_dashboard.php">
      INVENTARIS USTMANY
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navUser">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navUser">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item mx-2">
          <a class="nav-link" href="peminjaman.php">Peminjaman</a>
        </li>
        <li class="nav-item mx-2">
          <a class="nav-link" href="pengembalian.php">Pengembalian</a>
        </li>
        <li class="nav-item mx-2">
          <a class="nav-link" href="riwayat.php">Riwayat</a>
        </li>
        <!-- Dropdown Profil -->
        <li class="nav-item dropdown mx-2">
          <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?= $foto; ?>" alt="Profile" class="profile-img">
            <span><?= htmlspecialchars($user['display_name'] ?? $user['username']); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="user_profile.php">Profil Saya</a></li>
            <li><a class="dropdown-item" href="logout_user.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
