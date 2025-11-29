<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../index.php");
    exit;
}

require '../function.php';
include '../templates/header.php';
include '../templates/sidebar.php';

// ----------------------------
// SEARCH FILTER
// ----------------------------
$keyword = "";
if (isset($_GET['search'])) {
    $keyword = trim($_GET['search']);
}

// Query dengan join lengkap
$sql = "
    SELECT 
        e.id,
        e.tema_event,
        c.cabang AS nama_cabang
    FROM event e
    LEFT JOIN tempat t ON e.id_tempat = t.id
    LEFT JOIN tempat_panti tp ON t.id = tp.id_tempat
    LEFT JOIN tempat_umum tu ON t.id = tu.id_tempat
    LEFT JOIN partisipasi p ON e.id = p.id_event
    LEFT JOIN staff s ON p.id_staff = s.id
    LEFT JOIN cabang c ON s.id_cabang = c.id
";

// Kalau ada search → tambahkan filter
if ($keyword !== "") {
    $sql .= "
        WHERE 
            e.tema_event ILIKE '%$keyword%' OR
            c.cabang ILIKE '%$keyword%'
    ";
}

$sql .= " GROUP BY e.id, c.cabang ORDER BY e.id DESC";

$query = pg_query($koneksi, $sql);

?>

<div class="container mt-4">

    <h3>Daftar Event</h3>

    <a href="event-add.php" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Event
    </a>

    <!-- SEARCH BAR -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" placeholder="Cari tema event atau cabang..." 
                   value="<?= $keyword ?>" class="form-control">
            <button class="btn btn-dark" type="submit">Cari</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Tema Event</th>
                <th>Cabang</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php 
            $no = 1;
            while ($row = pg_fetch_assoc($query)): 
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['tema_event'] ?></td>

                    <!-- jika event belum ada daftar staff → cabang = '-' -->
                    <td><?= $row['nama_cabang'] ?: '-' ?></td>

                    <td>
                        <a href="event-detail.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Detail</a>
                        <a href="event-staff.php?id=<?= $row['id'] ?>" class="btn btn-secondary btn-sm">Staff</a>
                        <a href="event-edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>

                        <a href="event-delete.php?id=<?= $row['id'] ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin menghapus event?')">
                           Hapus
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>

    </table>

</div>

<?php include '../templates/footer.php'; ?>
