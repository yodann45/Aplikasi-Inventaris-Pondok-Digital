<?php
session_start();
include "../config.php";
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

// --- Aksi ---
if(isset($_GET['approve'])){
    $id = (int) $_GET['approve'];
    mysqli_query($koneksi, "UPDATE users SET status='approved' WHERE id=$id");
    header("Location: approve_users.php");
    exit;
}
if(isset($_GET['reject'])){
    $id = (int) $_GET['reject'];
    mysqli_query($koneksi, "UPDATE users SET status='rejected' WHERE id=$id");
    header("Location: approve_users.php");
    exit;
}
if(isset($_GET['delete'])){
    $id = (int) $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM users WHERE id=$id");
    header("Location: approve_users.php");
    exit;
}

// --- Filter status ---
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

$where = "role='tamu'";
if($status_filter != 'all'){
    $where .= " AND status='$status_filter'";
}

// --- Pagination ---
$limit = 10; // jumlah baris per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

// Hitung total user
$total_result = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users WHERE $where");
$total_users = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_users / $limit);

// Ambil user sesuai halaman
$users = mysqli_query($koneksi, "SELECT * FROM users WHERE $where ORDER BY id DESC LIMIT $limit OFFSET $offset");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include "navbar_admin.php"; ?>

<div class="container mt-4">
  <h2>Kelola User</h2>

  <!-- Filter -->
  <div class="mb-3">
    <a href="?status=all" class="btn btn-outline-secondary btn-sm <?= $status_filter=='all'?'active':'' ?>">Semua</a>
    <a href="?status=pending" class="btn btn-outline-warning btn-sm <?= $status_filter=='pending'?'active':'' ?>">Pending</a>
    <a href="?status=approved" class="btn btn-outline-success btn-sm <?= $status_filter=='approved'?'active':'' ?>">Approved</a>
    <a href="?status=rejected" class="btn btn-outline-danger btn-sm <?= $status_filter=='rejected'?'active':'' ?>">Rejected</a>
  </div>

  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>No</th>
        <th>Username</th>
        <th>Nama</th>
        <th>Phone</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $no = $offset + 1;
      while($row=mysqli_fetch_assoc($users)){ ?>
      <tr>
        <td><?= $no++; ?></td>
        <td><?= htmlspecialchars($row['username']); ?></td>
        <td><?= htmlspecialchars($row['display_name']); ?></td>
        <td><?= htmlspecialchars($row['phone']); ?></td>
        <td>
          <?php if($row['status']=='pending'){ ?>
            <span class="badge bg-warning">Pending</span>
          <?php } elseif($row['status']=='approved'){ ?>
            <span class="badge bg-success">Approved</span>
          <?php } else { ?>
            <span class="badge bg-danger">Rejected</span>
          <?php } ?>
        </td>
        <td>
          <?php if($row['status']=='pending'){ ?>
            <a href="?approve=<?= $row['id']; ?>&status=<?= $status_filter; ?>&page=<?= $page; ?>" class="btn btn-success btn-sm"
               onclick="return confirm('Setujui user ini?')">Setujui</a>
            <a href="?reject=<?= $row['id']; ?>&status=<?= $status_filter; ?>&page=<?= $page; ?>" class="btn btn-danger btn-sm"
               onclick="return confirm('Tolak user ini?')">Tolak</a>
          <?php } elseif($row['status']=='approved'){ ?>
            <a href="?reject=<?= $row['id']; ?>&status=<?= $status_filter; ?>&page=<?= $page; ?>" class="btn btn-warning btn-sm"
               onclick="return confirm('Ubah jadi Rejected?')">Reject</a>
          <?php } else { ?>
            <a href="?approve=<?= $row['id']; ?>&status=<?= $status_filter; ?>&page=<?= $page; ?>" class="btn btn-success btn-sm"
               onclick="return confirm('Ubah jadi Approved?')">Approve</a>
          <?php } ?>
          <a href="?delete=<?= $row['id']; ?>&status=<?= $status_filter; ?>&page=<?= $page; ?>" class="btn btn-outline-danger btn-sm"
             onclick="return confirm('Hapus user ini?')">Hapus</a>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <nav>
    <ul class="pagination">
      <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
        <a class="page-link" href="?status=<?= $status_filter; ?>&page=<?= $page-1; ?>">Previous</a>
      </li>
      <?php for($i=1; $i <= $total_pages; $i++){ ?>
        <li class="page-item <?= ($i==$page)?'active':'' ?>">
          <a class="page-link" href="?status=<?= $status_filter; ?>&page=<?= $i; ?>"><?= $i; ?></a>
        </li>
      <?php } ?>
      <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
        <a class="page-link" href="?status=<?= $status_filter; ?>&page=<?= $page+1; ?>">Next</a>
      </li>
    </ul>
  </nav>

</div>
</body>
</html>
