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

// Ambil data event + tempat
$query = pg_query_params(
    $koneksi,
    "SELECT e.*, 
            tu.ruang,
            tp.nama_panti
     FROM event e
     LEFT JOIN tempat_umum tu ON e.id_tempat = tu.id_tempat
     LEFT JOIN tempat_panti tp ON e.id_tempat = tp.id_tempat
     WHERE e.id = $1",
    [$id]
);
$data = pg_fetch_assoc($query);

// Ambil semua tempat
$tempat = pg_query($koneksi, "SELECT id FROM tempat ORDER BY id ASC");

$success = "";
$error = "";

if (isset($_POST['submit'])) {

    $tema_event = $_POST['tema_event'];
    $id_tempat  = $_POST['id_tempat'];

    $update = "UPDATE event SET tema_event = $1, id_tempat = $2 WHERE id = $3";

    $res = pg_query_params($koneksi, $update, [
        $tema_event, $id_tempat, $id
    ]);

    if ($res) $success = "Event berhasil diupdate!";
    else $error = "Gagal update event!";
}
?>

<div class="container mt-4">

    <h3>Edit Event</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">

        <div class="mb-3">
            <label class="form-label">Tema Event</label>
            <input type="text" name="tema_event" class="form-control" 
                   value="<?= $data['tema_event'] ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tempat</label>
            <select name="id_tempat" class="form-control" required>
                <?php while ($t = pg_fetch_assoc($tempat)): ?>
                    <option value="<?= $t['id'] ?>"
                        <?= ($t['id'] == $data['id_tempat']) ? 'selected' : '' ?>>
                        Tempat ID: <?= $t['id'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Update</button>
        <a href="event-list.php" class="btn btn-secondary">Kembali</a>

    </form>

</div>

<?php include '../templates/footer.php'; ?>
