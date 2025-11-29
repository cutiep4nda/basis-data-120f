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

// Ambil data staff berdasarkan ID
$query = pg_query_params($koneksi, "SELECT * FROM staff WHERE id = $1", [$id]);
$data  = pg_fetch_assoc($query);

$success = "";
$error = "";

if (isset($_POST['submit'])) {

    $nama          = $_POST['nama'];
    $tempat_lahir  = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $mbti          = $_POST['mbti'];
    $instansi      = $_POST['instansi'];
    $id_cabang     = $_POST['id_cabang'];
    $id_divisi     = $_POST['id_divisi'];

    $update = "
        UPDATE staff 
        SET nama=$1, tempat_lahir=$2, tanggal_lahir=$3, mbti=$4, instansi=$5, id_cabang=$6, id_divisi=$7
        WHERE id=$8
    ";

    $result = pg_query_params($koneksi, $update, [
        $nama, $tempat_lahir, $tanggal_lahir, $mbti, $instansi, $id_cabang, $id_divisi, $id
    ]);

    if ($result) {
        $success = "Data staff berhasil diperbarui!";
    } else {
        $error = "Gagal memperbarui data!";
    }
}

?>

<div class="container mt-4">

    <h3>Edit Staff</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">

        <!-- Nama -->
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" value="<?= $data['nama'] ?>" required>
        </div>

        <!-- Tempat Lahir -->
        <div class="mb-3">
            <label class="form-label">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" class="form-control" value="<?= $data['tempat_lahir'] ?>">
        </div>

        <!-- Tanggal Lahir -->
        <div class="mb-3">
            <label class="form-label">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control" value="<?= $data['tanggal_lahir'] ?>">
        </div>

        <!-- MBTI -->
        <div class="mb-3">
            <label class="form-label">MBTI</label>
            <input type="text" name="mbti" class="form-control" value="<?= $data['mbti'] ?>">
        </div>

        <!-- Instansi -->
        <div class="mb-3">
            <label class="form-label">Instansi</label>
            <input type="text" name="instansi" class="form-control" value="<?= $data['instansi'] ?>">
        </div>

        <!-- Cabang -->
        <div class="mb-3">
            <label class="form-label">Cabang</label>
            <select name="id_cabang" class="form-control" required>
                <?php
                $cb = pg_query($koneksi, "SELECT * FROM cabang ORDER BY cabang ASC");
                while ($c = pg_fetch_assoc($cb)) :
                ?>
                    <option value="<?= $c['id']; ?>"
                        <?= $c['id'] == $data['id_cabang'] ? 'selected' : '' ?>>
                        <?= $c['cabang']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Divisi -->
        <div class="mb-3">
            <label class="form-label">Divisi</label>
            <select name="id_divisi" class="form-control" required>
                <?php
                $dv = pg_query($koneksi, "SELECT * FROM divisi ORDER BY di_name ASC");
                while ($d = pg_fetch_assoc($dv)) :
                ?>
                    <option value="<?= $d['id']; ?>"
                        <?= $d['id'] == $data['id_divisi'] ? 'selected' : '' ?>>
                        <?= $d['di_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Update</button>
        <a href="staff-list.php" class="btn btn-secondary">Kembali</a>

    </form>

</div>

<?php include '../templates/footer.php'; ?>
