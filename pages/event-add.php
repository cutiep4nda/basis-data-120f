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

    $nama_event = $_POST['nama_event'];
    $tanggal    = $_POST['tanggal'];
    $lokasi     = $_POST['lokasi'];
    $deskripsi  = $_POST['deskripsi'];

    $query = "INSERT INTO event (nama_event, tanggal, lokasi, deskripsi)
              VALUES ($1, $2, $3, $4)";

    $result = pg_query_params($koneksi, $query, [
        $nama_event, $tanggal, $lokasi, $deskripsi
    ]);

    if ($result) $success = "Event berhasil ditambahkan!";
    else $error = "Gagal menambah event!";
}

?>

<div class="container mt-4">

    <h3>Tambah Event</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">

        <div class="mb-3">
            <label class="form-label">Nama Event</label>
            <input type="text" name="nama_event" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Lokasi</label>
            <input type="text" name="lokasi" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control"></textarea>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        <a href="event-list.php" class="btn btn-secondary">Kembali</a>

    </form>

</div>

<?php include '../templates/footer.php'; ?>
