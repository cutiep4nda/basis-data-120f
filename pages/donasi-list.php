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

    <h3>Daftar Donasi</h3>

    <a href="donasi-add.php" class="btn btn-primary mb-3">
        <i class="bi bi-plus-circle"></i> Tambah Donasi
    </a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Donatur</th>
                <th>Jenis</th>
                <th>Keterangan/Jumlah</th>
                <th>Nominal (Rp)</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $query = pg_query($koneksi, "
                SELECT 
                    d.id,
                    d.tanggal,
                    dn.nama,
                    du.nominal,
                    db.keterangan,
                    db.kuantitas
                FROM donasi d
                JOIN donatur dn ON d.id_donatur = dn.id
                LEFT JOIN donasi_uang du ON du.id_donasi = d.id
                LEFT JOIN donasi_barang db ON db.id_donasi = d.id
                ORDER BY d.id DESC
            ");

            $no = 1;
            while ($row = pg_fetch_assoc($query)) :

                // menentukan jenis donasi
                if ($row['nominal'] !== null) {
                    $jenis = "Uang";
                } else {
                    $jenis = "Barang";
                }
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['nama']; ?></td>
                    <td><?= $jenis; ?></td>

                    <td>
                        <?php if ($jenis == "Barang"): ?>
                            <?= $row['keterangan']; ?> (<?= $row['kuantitas']; ?> pcs)
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($jenis == "Uang"): ?>
                            Rp <?= number_format($row['nominal'], 0, ',', '.'); ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>

                    <td><?= $row['tanggal']; ?></td>

                    <td>
                        <a href="donasi-edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="donasi-delete.php?id=<?= $row['id']; ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin menghapus donasi ini?')">
                           Hapus
                        </a>
                    </td>
                </tr>

            <?php endwhile; ?>
        </tbody>

    </table>

</div>

<?php include '../templates/footer.php'; ?>
