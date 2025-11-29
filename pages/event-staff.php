<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../index.php");
    exit;
}

require '../function.php';
include '../templates/header.php';
include '../templates/sidebar.php';

// Pastikan parameter id_event ada
if (!isset($_GET['id'])) {
    die("<div class='container mt-4'><h3>Error: ID event tidak ditemukan!</h3></div>");
}

$id_event = $_GET['id'];

// Ambil data event
$event = pg_fetch_assoc(pg_query_params(
    $koneksi,
    "SELECT * FROM event WHERE id = $1",
    [$id_event]
));

if (!$event) {
    die("<div class='container mt-4'><h3>Error: Event tidak ditemukan!</h3></div>");
}

// Ambil semua staff
$staff = pg_query($koneksi, "SELECT * FROM staff ORDER BY nama ASC");

// Ambil staff yang sudah ikut event (tabel: partisipasi)
$assigned = pg_query_params(
    $koneksi,
    "SELECT id_staff FROM partisipasi WHERE id_event = $1",
    [$id_event]
);

$assigned_ids = [];
while ($a = pg_fetch_assoc($assigned)) {
    $assigned_ids[] = $a['id_staff'];
}

// SUBMIT UPDATE
if (isset($_POST['submit'])) {

    // Hapus semua partisipasi staff sebelumnya
    pg_query_params(
        $koneksi,
        "DELETE FROM partisipasi WHERE id_event = $1",
        [$id_event]
    );

    // Tambahkan kembali sesuai checkbox
    if (!empty($_POST['staff'])) {
        foreach ($_POST['staff'] as $id_staff) {
            pg_query_params(
                $koneksi,
                "INSERT INTO partisipasi (id_staff, id_event) VALUES ($1, $2)",
                [$id_staff, $id_event]
            );
        }
    }

    $msg = "Staff berhasil diperbarui!";
}
?>

<div class="container mt-4">
    <h3>Atur Staff untuk Event: <b><?= $event['tema_event'] ?></b></h3>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST">

        <?php while ($row = pg_fetch_assoc($staff)): ?>
            <div class="form-check">
                <input type="checkbox"
                       class="form-check-input"
                       name="staff[]"
                       value="<?= $row['id'] ?>"
                       <?= in_array($row['id'], $assigned_ids) ? "checked" : "" ?>>

                <label class="form-check-label">
                    <?= $row['nama'] ?> — <?= $row['instansi'] ?>
                </label>
            </div>
        <?php endwhile; ?>

        <button type="submit" name="submit" class="btn btn-primary mt-3">Simpan</button>
        <a href="event-list.php" class="btn btn-secondary mt-3">Kembali</a>

    </form>

</div>

<?php include '../templates/footer.php'; ?>
