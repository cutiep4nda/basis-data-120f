<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../index.php");
    exit;
}

require '../function.php';
include '../templates/header.php';
include '../templates/sidebar.php';

$success = "";
$error = "";

if (isset($_POST['submit'])) {

    $id_donatur = $_POST['id_donatur'];
    $jenis      = $_POST['jenis'];
    $jumlah     = $_POST['jumlah'];
    $nilai      = $_POST['nilai'];
    $tanggal    = $_POST['tanggal'];

    $query = "INSERT INTO donasi 
    (id_donatur, jenis, jumlah, nilai_uang, tanggal) 
    VALUES ($1, $2, $3, $4, $5)";

    $result = pg_query_params($koneksi, $query, [
        $id_donatur, $jenis, $jumlah, $nilai, $tanggal
    ]);

    if ($result) {
        $success = "Donasi berhasil ditambahkan!";
    } else {
        $error = "Gagal menambah donasi!";
    }
}

?>

<div class="container mt-4">

    <h3>Tambah Donasi</h3>

    <?php if ($success) : ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error) : ?>
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
                while ($dn = pg_fetch_assoc($d)) :
                ?>
                    <option value="<?= $dn['id_donatur']; ?>">
                        <?= $dn['nama']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Jenis Donasi -->
        <div class="mb-3">
            <label class="form-label">Jenis Donasi</label>
            <select name="jenis" class="form-control" required>
                <option value="">-- pilih jenis --</option>
                <option value="uang">Uang</option>
                <option value="barang">Barang</option>
            </select>
        </div>

        <!-- Jumlah -->
        <div class="mb-3">
            <label class="form-label">Jumlah</label>
            <input type="number" name="jumlah" class="form-control" required>
        </div>

        <!-- Nilai Uang -->
        <div class="mb-3">
            <label class="form-label">Nilai Donasi (Jika Uang)</label>
            <input type="number" name="nilai" class="form-control">
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

<?php include '../templates/footer.php'; ?>
