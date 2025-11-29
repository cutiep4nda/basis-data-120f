<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../index.php");
    exit;
}

require '../function.php';
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<div class="container mt-4">
    <h3>Daftar Donatur</h3>

    <a href="donatur-add.php" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Donatur
    </a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Donatur</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php
            // Query sesuai database
            $query = pg_query($koneksi, "SELECT * FROM donatur ORDER BY id DESC");
            $no = 1;

            while ($row = pg_fetch_assoc($query)) :
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['nama']; ?></td>

                    <td>
                        <a href="donatur-edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        <a href="donatur-delete.php?id=<?= $row['id']; ?>"
                           onclick="return confirm('Yakin ingin menghapus donatur ini?');"
                           class="btn btn-danger btn-sm">
                           Hapus
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include '../templates/footer.php'; ?>
