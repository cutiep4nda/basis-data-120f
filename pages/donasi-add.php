<?php

require '../config/db.php';
include '../templates/header.php';
include '../templates/sidebar.php';

$success = "";
$error = "";

if (isset($_POST['submit'])) {

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // exit;

    $id_donatur = $_POST['id_donatur'];
    $jenis = $_POST['jenis'];
    $tanggal = $_POST['tanggal'];

    pg_query($koneksi, "BEGIN");

    $query_donasi = "INSERT INTO donasi (id_donatur, tanggal) VALUES ($1, $2) RETURNING id";
    $result_donasi = pg_query_params($koneksi, $query_donasi, [$id_donatur, $tanggal]);

    if ($result_donasi) {
        $donasi = pg_fetch_assoc($result_donasi);
        $id_donasi = $donasi['id'];

        if ($jenis == 'uang') {
            $nominal = $_POST['nominal'];
            $query_uang = "INSERT INTO donasi_uang (id_donasi, nominal) VALUES ($1, $2)";
            $result_detail = pg_query_params($koneksi, $query_uang, [$id_donasi, $nominal]);

        } elseif ($jenis == 'barang') {
            $keterangan = $_POST['keterangan'];
            $kuantitas = $_POST['kuantitas'];
            $query_barang = "INSERT INTO donasi_barang (id_donasi, keterangan, kuantitas) VALUES ($1, $2, $3)";
            $result_detail = pg_query_params($koneksi, $query_barang, [$id_donasi, $keterangan, $kuantitas]);
        }

        if ($result_detail) {
            pg_query($koneksi, "COMMIT");
            $success = "Donasi berhasil ditambahkan!";
        } else {
            pg_query($koneksi, "ROLLBACK");
            $error = "Gagal menambahkan detail donasi!";
        }

    } else {
        pg_query($koneksi, "ROLLBACK");
        $error = "Gagal menambahkan donasi!";
    }
}
?>

<div class="container mt-4">

    <h3>Tambah Donasi</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">

        <!-- Pilih Donatur -->
        <div class="mb-3">
            <label class="form-label">Pilih Donatur</label>
            <select name="id_donatur" class="form-control" required>
                <option value="">-- pilih donatur --</option>

                <?php
                $d = pg_query($koneksi, "SELECT * FROM donatur ORDER BY nama ASC");
                while ($dn = pg_fetch_assoc($d)):
                    ?>
                    <option value="<?= $dn['id']; ?>">
                        <?= $dn['nama']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Jenis Donasi -->
        <div class="mb-3">
            <label class="form-label">Jenis Donasi</label>
            <select name="jenis" class="form-control" id="jenis" required>
                <option value="">-- pilih jenis --</option>
                <option value="uang">Uang</option>
                <option value="barang">Barang</option>
            </select>
        </div>

        <!-- Nominal (donasi uang) -->
        <div class="mb-3" id="nominal_box" style="display:none;">
            <label class="form-label">Nominal (Rp)</label>
            <input type="number" name="nominal" class="form-control">
        </div>

        <!-- Keterangan (donasi barang) -->
        <div class="mb-3" id="keterangan_box" style="display:none;">
            <label class="form-label">Keterangan Barang</label>
            <input type="text" name="keterangan" class="form-control">
        </div>

        <!-- Kuantitas (donasi barang) -->
        <div class="mb-3" id="kuantitas_box" style="display:none;">
            <label class="form-label">Kuantitas Barang</label>
            <input type="number" name="kuantitas" class="form-control">
        </div>

        <!-- Tanggal -->
        <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>

        <a href="donasi-list.php" class="btn btn-secondary">Kembali</a>

    </form>

</div>

<script>
    document.getElementById('jenis').addEventListener('change', function () {
        let jenis = this.value;
        document.getElementById('nominal_box').style.display = (jenis === "uang") ? "block" : "none";
        document.getElementById('keterangan_box').style.display = (jenis === "barang") ? "block" : "none";
        document.getElementById('kuantitas_box').style.display = (jenis === "barang") ? "block" : "none";
    });
</script>

<?php include '../templates/footer.php'; ?>