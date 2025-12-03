<?php
require '../../config/db.php';
include '../../templates/header.php';
include '../../templates/sidebar.php';

if (!isset($_GET['id'])) {
    die("<div class='container mt-4'><h3>Error: ID event tidak ditemukan!</h3></div>");
}

$id_event = $_GET['id'];

$event = pg_fetch_assoc(pg_query_params(
    $koneksi,
    "SELECT * FROM event WHERE id = $1",
    [$id_event]
));

$staff = pg_query($koneksi, "SELECT * FROM staff ORDER BY nama ASC");

$partisipasi = pg_query_params(
    $koneksi,
    "SELECT id_staff FROM partisipasi WHERE id_event = $1",
    [$id_event]
);

$assigned_ids = [];
$ketua_pelaksana = null;

while ($p = pg_fetch_assoc($partisipasi)) {
    if ($ketua_pelaksana === null) {
        $ketua_pelaksana = $p['id_staff'];
    } else {
        $assigned_ids[] = $p['id_staff'];
    }
}

if (isset($_POST['submit'])) {

    $ketua = $_POST['ketua_pelaksana'];
    $staff_selected = !empty($_POST['staff']) ? $_POST['staff'] : [];

    pg_query_params($koneksi, "DELETE FROM partisipasi WHERE id_event = $1", [$id_event]);

    pg_query_params(
        $koneksi,
        "INSERT INTO partisipasi (id_staff, id_event) VALUES ($1, $2)",
        [$ketua, $id_event]
    );

    foreach ($staff_selected as $s) {
        pg_query_params(
            $koneksi,
            "INSERT INTO partisipasi (id_staff, id_event) VALUES ($1, $2)",
            [$s, $id_event]
        );
    }

    $msg = "Staff & Ketua Pelaksana berhasil diperbarui!";
}
?>

<div class="container mt-4">
    <h3>Atur Staff Event: <b><?= $event['tema_event'] ?></b></h3>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>

    <form method="POST">

        <!-- Dropdown Ketua Pelaksana -->
        <div class="mb-3">
            <label class="form-label"><b>Ketua Pelaksana</b></label>
            <select name="ketua_pelaksana" class="form-control" required>
                <option value="">-- pilih ketua pelaksana --</option>
                <?php
                $staff_all = pg_query($koneksi, "SELECT * FROM staff ORDER BY nama ASC");
                while ($row = pg_fetch_assoc($staff_all)): ?>
                    <option value="<?= $row['id'] ?>" <?= $row['id'] == $ketua_pelaksana ? "selected" : "" ?>>
                        <?= $row['nama'] ?> — <?= $row['instansi'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <hr>

        <!-- Checkbox Staff lainnya -->
        <label class="form-label"><b>Staff Anggota (selain ketua)</b></label>
        <?php
        $staff_check = pg_query($koneksi, "SELECT * FROM staff ORDER BY nama ASC");
        while ($row = pg_fetch_assoc($staff_check)):
            if ($row['id'] == $ketua_pelaksana)
                continue; // hide ketua from checkbox
            ?>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" name="staff[]" value="<?= $row['id'] ?>"
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

<?php include '../../templates/footer.php'; ?>