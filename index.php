<?php
include "config.php";

// Konfigurasi pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : "";

// Konfigurasi paginasi
$limit = 5; // jumlah barang per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Query total data (filter pencarian)
$where = "WHERE status='aktif'";
if (!empty($search)) {
    $where .= " AND (nama_barang LIKE '%$search%' OR keterangan LIKE '%$search%')";
}

$total_query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang $where");
$total_data  = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

// Query data barang (filter + limit)
$barang = mysqli_query($koneksi, "SELECT * FROM barang $where LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Inventaris Pondok</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
body { 
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
}

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
  color: #d4edda; /* hijau muda */
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

/* Toggler button */
.navbar-toggler {
  border: none;
  outline: none;
}
.navbar-toggler:focus {
  box-shadow: none;
}


/* Hero Section */
.hero {
  position: relative;
  background: url('assets/pondok.jpg') center/cover no-repeat;
  color: white;
  text-shadow: 0 2px 5px rgba(0,0,0,0.6);
  padding: 120px 20px;
  text-align: center;
  z-index: 0;
  border-bottom: 5px solid #198754;
}

.hero::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  z-index: -1;
}

.hero h1 { 
  font-size: 3rem; 
  font-weight: bold; 
  animation: fadeInDown 1.2s ease; 
}
.hero p { 
  font-size: 1.2rem; 
  margin-top: 10px; 
  animation: fadeInUp 1.5s ease; 
}

/* Button Styling */
.btn {
  border-radius: 30px;
  transition: all 0.3s ease;
  font-weight: 500;
  
}
.btn-success:hover, 
.btn-light:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

/* Section */
.section { 
  padding: 60px 0; 
}

/* Table */
.table {
  border-radius: 10px;
  overflow: hidden;
}
.table thead {
  background: linear-gradient(90deg, #198754, #28a745);
  color: #fff;
}

/* Pagination */
.pagination .page-link {
  border-radius: 50%;
  margin: 0 3px;
  transition: all 0.3s;
}
.pagination .page-link:hover {
  background: #198754;
  color: #fff;
}

/* Card */
.card {
  border-radius: 15px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}
.card-body h4 {
  color: #198754;
  font-weight: bold;
}

/* Footer */
footer { 
  background: #198754; 
  color: white; 
  padding: 20px 0; 
  margin-top: 40px; 
  box-shadow: 0 -3px 10px rgba(0,0,0,0.2);
}

/* Animations */
@keyframes fadeInDown {
  from { opacity: 0; transform: translateY(-30px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

  </style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark shadow custom-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold fs-4" href="index.php">
      INVENTARIS USTMANY
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item mx-2">
          <a class="nav-link active" href="index.php">Home</a>
        </li>
        <li class="nav-item mx-2">
          <a class="nav-link" href="login_user.php">Login Tamu</a>
        </li>
        <li class="nav-item mx-2">
          <a class="nav-link" href="admin/login.php">Login Admin</a>
        </li>
      </ul>
    </div>
  </div>
</nav>


<!-- Hero Section -->
<section class="hero">
  <div class="container">
    <h1>Selamat Datang di Sistem Inventaris Pondok</h1>
    <p>Mengelola peminjaman & pengembalian barang pondok dengan mudah, cepat, dan transparan.</p>
    <a href="login_user.php" class="btn btn-light btn-lg mt-3">Mulai Gunakan</a>
  </div>
</section>

<!-- Daftar Barang -->
<section class="section bg-white">
  <div class="container">
    <h2 class="mb-4 text-center">Daftar Barang Tersedia</h2>

    <!-- Search Form -->
    <form method="get" class="mb-3 d-flex justify-content-center">
      <input type="text" name="search" class="form-control w-50 me-2" placeholder="Cari barang..." value="<?= htmlspecialchars($search); ?>">
      <button type="submit" class="btn btn-success">Cari</button>
    </form>

    <div class="table-responsive shadow rounded">
      <table class="table table-bordered table-hover align-middle">
        <thead class="table-success text-center">
          <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Stok</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          if (mysqli_num_rows($barang) > 0) {
            $no = $offset + 1;
            while($row = mysqli_fetch_assoc($barang)){ ?>
              <tr>
                <td class="text-center"><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nama_barang']); ?></td>
                <td class="text-center"><?= $row['stok']; ?></td>
                <td><?= htmlspecialchars($row['keterangan']); ?></td>
              </tr>
          <?php } 
          } else { ?>
            <tr>
              <td colspan="4" class="text-center">Tidak ada barang ditemukan.</td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

    <!-- Paginasi -->
    <nav>
      <ul class="pagination justify-content-center mt-4">
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
          <a class="page-link" href="?search=<?= urlencode($search); ?>&page=<?= $page-1; ?>">Previous</a>
        </li>
        <?php for($i=1; $i <= $total_pages; $i++){ ?>
          <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
            <a class="page-link" href="?search=<?= urlencode($search); ?>&page=<?= $i; ?>"><?= $i; ?></a>
          </li>
        <?php } ?>
        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
          <a class="page-link" href="?search=<?= urlencode($search); ?>&page=<?= $page+1; ?>">Next</a>
        </li>
      </ul>
    </nav>
  </div>
</section>

<!-- Tentang Pondok -->
<section class="section">
  <div class="container text-center">
    <h2 class="mb-4">Tentang Pondok Pesantren</h2>
    <p class="lead">
      Pondok Pesantren kami berkomitmen mendidik santri dengan ilmu agama & keterampilan modern.
      Dengan sistem inventaris digital ini, kami menghadirkan layanan peminjaman barang yang lebih
      teratur, transparan, dan mudah digunakan baik untuk santri maupun pengurus.
    </p>
  </div>
</section>

<!-- Cara Menggunakan -->
<section class="section text-white bg-success ">
  <div class="container">
    <h2 class="text-center mb-5">Cara Menggunakan Sistem</h2>
    <div class="row text-center">
      <div class="col-md-4 mb-4">
        <div class="card h-100 shadow border-0">
          <div class="card-body">
            <h4>1. Daftar / Login</h4>
            <p>Buat akun tamu atau login untuk mengakses sistem inventaris pondok.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card h-100 shadow border-0">
          <div class="card-body">
            <h4>2. Pinjam Barang</h4>
            <p>Pilih barang yang tersedia, isi jumlah & tanggal, lalu kirim permintaan peminjaman.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card h-100 shadow border-0">
          <div class="card-body">
            <h4>3. Kembalikan Barang</h4>
            <p>Setelah selesai, kembalikan barang melalui sistem agar tercatat secara resmi.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Promosi Pondok -->
<section class="section text-center">
  <div class="container">
    <h2 class="mb-3">Mengapa Memilih Pondok Kami?</h2>
    <p class="lead">
      ✔️ Pendidikan berbasis akhlak & ilmu agama <br>
      ✔️ Fasilitas lengkap & modern <br>
      ✔️ Lingkungan Islami yang kondusif <br>
      ✔️ Sistem manajemen pondok yang transparan & digital
    </p>
  </div>
</section>

<!-- Footer -->
<footer class="text-center">
  <div class="container">
    <p class="mb-0">&copy; <?= date('Y'); ?> Cak Dani F | Sistem Inventaris Digital</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
