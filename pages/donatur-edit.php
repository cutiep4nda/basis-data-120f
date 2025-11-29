<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../index.php");
    exit;
}

require '../function.php';
include '../templates/header.php';
include '../templates/sidebar.php';

$id = $_GET['id'];

$query = pg_query_params($koneksi, "SELECT * FROM donatur WHERE id = $1", [$id]);
$data = pg_fetch_assoc($query);

$success = "";
$error = "";

if (isset($_POST['submit'])) {

    $nama = $_POST['nama'];

    $update = "UPDATE donatur SET nama = $1 WHERE id = $2";
    $result = pg_query_params($koneksi, $update, [$nama, $id]);

    if ($result) {
        $success = "Data donatur berhasil diperbarui!";

        $query = pg_query_params($koneksi, "SELECT * FROM donatur WHERE id = $1", [$id]);
        $data = pg_fetch_assoc($query);
    } else {
        $error = "Gagal memperbarui data!";
    }
}

?>

<div class="container mt-4">
    <h3>Edit Donatur</h3>

    <?php if ($success) : ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">

        <div class="mb-3">
            <label class="form-label">Nama Donatur</label>
            <input type="text" name="nama" class="form-control" required 
                   value="<?= htmlspecialchars($data['nama']) ?>">
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Update</button>
        <a href="donatur-list.php" class="btn btn-secondary">Kembali</a>

    </form>

</div>

<?php include '../templates/footer.php'; ?>
