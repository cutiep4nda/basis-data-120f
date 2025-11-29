<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../index.php");
    exit;
}

require '../function.php';
include '../templates/header.php';
include '../templates/sidebar.php';

// Ambil keyword pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
?>

<div class="container mt-4">
    <h3>Daftar Staff</h3>

    <!-- FORM SEARCH -->
    <form method="GET" class="mb-3 d-flex" style="max-width: 400px;">
        <input type="text" name="search" class="form-control me-2"
               placeholder="Cari nama, instansi, MBTI..."
               value="<?= htmlspecialchars($search); ?>">
        <button class="btn btn-primary"><i class="bi bi-search"></i></button>
    </form>

    <a href="staff-add.php" class="btn btn-success mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Staff
    </a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>MBTI</th>
                <th>Instansi</th>
                <th>Cabang</th>
                <th>Divisi</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php
            // Jika search kosong → tampilkan semua
            if ($search == "") {
                $query = pg_query($koneksi, "
                    SELECT s.*, c.cabang, d.di_name AS divisi
                    FROM staff s
                    JOIN cabang c ON s.id_cabang = c.id
                    JOIN divisi d ON s.id_divisi = d.id
                    ORDER BY s.id DESC
                ");
            } else {
                // Search → LIKE case-insensitive
                $query = pg_query_params($koneksi, "
                    SELECT s.*, c.cabang, d.di_name AS divisi
                    FROM staff s
                    JOIN cabang c ON s.id_cabang = c.id
                    JOIN divisi d ON s.id_divisi = d.id
                    WHERE 
                        LOWER(s.nama) LIKE LOWER($1)
                        OR LOWER(s.instansi) LIKE LOWER($1)
                        OR LOWER(s.mbti) LIKE LOWER($1)
                    ORDER BY s.id DESC
                ", ["%$search%"]);
            }

            $no = 1;
            while ($row = pg_fetch_assoc($query)) :
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['nama']; ?></td>
                    <td><?= $row['tempat_lahir']; ?></td>
                    <td><?= $row['tanggal_lahir']; ?></td>
                    <td><?= $row['mbti']; ?></td>
                    <td><?= $row['instansi']; ?></td>
                    <td><?= $row['cabang']; ?></td>
                    <td><?= $row['divisi']; ?></td>

                    <td>
                        <a href="staff-edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>

                        <a href="staff-delete.php?id=<?= $row['id']; ?>"
                           onclick="return confirm('Yakin ingin menghapus staff ini?');"
                           class="btn btn-danger btn-sm">Hapus</a>
                    </td>
                </tr>

            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../templates/footer.php'; ?>
