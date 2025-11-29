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

// Ambil cabang untuk dropdown
$cabangQuery = pg_query($koneksi, "SELECT * FROM cabang ORDER BY cabang ASC");
$divisiQuery = pg_query($koneksi, "SELECT * FROM divisi ORDER BY di_name ASC");

if (isset($_POST['submit'])) {

    $nama          = $_POST['nama'];
    $tempat_lahir  = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $mbti          = $_POST['mbti'];
    $instansi      = $_POST['instansi'];
    $id_cabang     = $_POST['id_cabang'];
    $id_divisi     = $_POST['id_divisi'];

    $query = "
        INSERT INTO staff 
        (nama, tempat_lahir, tanggal_lahir, mbti, instansi, id_cabang, id_divisi)
        VALUES ($1, $2, $3, $4, $5, $6, $7)
    ";

    $result = pg_query_params($koneksi, $query, [
        $nama, $tempat_lahir, $tanggal_lahir, $mbti, $instansi,
        $id_cabang, $id_divisi
    ]);

    if ($result) {
        $success = "Staff berhasil ditambahkan!";
    } else {
        $error = "Gagal menambah staff!";
    }
}
?>

<div class="container mt-4">

    <h3>Tambah Staff</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">

        <div class="mb-3">
            <label class="form-label">Nama Staff</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">MBTI</label>
            <input type="text" name="mbti" class="form-control" placeholder="Contoh: INFP, ESTJ" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Instansi</label>
            <input type="text" name="instansi" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Cabang</label>
            <select name="id_cabang" class="form-control" required>
                <option value="">-- Pilih Cabang --</option>
                <?php while ($c = pg_fetch_assoc($cabangQuery)) : ?>
                    <option value="<?= $c['id']; ?>"><?= $c['cabang']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Divisi</label>
            <select name="id_divisi" class="form-control" required>
                <option value="">-- Pilih Divisi --</option>
                <?php while ($d = pg_fetch_assoc($divisiQuery)) : ?>
                    <option value="<?= $d['id']; ?>"><?= $d['di_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        <a href="staff-list.php" class="btn btn-secondary">Kembali</a>

    </form>

</div>

<?php include '../templates/footer.php'; ?>