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

    $nama = $_POST['nama'];

    $query = "INSERT INTO donatur (nama) VALUES ($1)";
    $result = pg_query_params($koneksi, $query, [$nama]);

    if ($result) {
        $success = "Donatur berhasil ditambahkan!";
    } else {
        $error = "Gagal menambah donatur!";
    }
}

?>

<div class="container mt-4">
    <h3>Tambah Donatur</h3>

    <?php if ($success) : ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">

        <div class="mb-3">
            <label class="form-label">Nama Donatur</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        <a href="donatur-list.php" class="btn btn-secondary">Kembali</a>

    </form>
</div>

<?php include '../templates/footer.php'; ?>
