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

// Ambil data donasi utama
$donasi = pg_query_params($koneksi, 
    "SELECT * FROM donasi WHERE id = $1", [$id]
);
$data = pg_fetch_assoc($donasi);

// Cek apakah donasi uang
$q_uang = pg_query_params($koneksi,
    "SELECT * FROM donasi_uang WHERE id_donasi = $1", [$id]
);
$data_uang = pg_fetch_assoc($q_uang);

// Cek apakah donasi barang
$q_barang = pg_query_params($koneksi,
    "SELECT * FROM donasi_barang WHERE id_donasi = $1", [$id]
);
$data_barang = pg_fetch_assoc($q_barang);

// Tentukan jenis donasi
$jenis = "uang";
if ($data_barang) $jenis = "barang";

$success = "";
$error = "";

if (isset($_POST['submit'])) {

    $id_donatur = $_POST['id_donatur'];
    $tanggal = $_POST['tanggal'];
    $jenis_baru = $_POST['jenis'];

    // Update tabel donasi
    pg_query_params($koneksi,
        "UPDATE donasi SET id_donatur = $1, tanggal = $2 WHERE id = $3",
        [$id_donatur, $tanggal, $id]
    );

    // Jika jenis uang
    if ($jenis_baru == "uang") {
        $nominal = $_POST['nominal'];

        // Hapus data barang jika ada
        pg_query_params($koneksi,
            "DELETE FROM donasi_barang WHERE id_donasi = $1", [$id]
        );

        // Insert/update uang
        if ($data_uang) {
            pg_query_params($koneksi,
                "UPDATE donasi_uang SET nominal = $1 WHERE id_donasi = $2",
                [$nominal, $id]
            );
        } else {
            pg_query_params($koneksi,
                "INSERT INTO donasi_uang (id_donasi, nominal) VALUES ($1, $2)",
                [$id, $nominal]
            );
        }
    }

    // Jika jenis barang
    else {
        $ket = $_POST['keterangan'];
        $qty = $_POST['kuantitas'];

        // Hapus data uang jika ada
        pg_query_params($koneksi,
            "DELETE FROM donasi_uang WHERE id_donasi = $1", [$id]
        );

        // Insert/update barang
        if ($data_barang) {
            pg_query_params($koneksi,
                "UPDATE donasi_barang 
                 SET keterangan = $1, kuantitas = $2 
                 WHERE id_donasi = $3",
                [$ket, $qty, $id]
            );
        } else {
            pg_query_params($koneksi,
                "INSERT INTO donasi_barang (id_donasi, keterangan, kuantitas)
                 VALUES ($1, $2, $3)",
                [$id, $ket, $qty]
            );
        }
    }

    $success = "Donasi berhasil diperbarui!";
}

?>

<div class="container mt-4">

    <h3>Edit Donasi</h3>

    <?php if ($success) : ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">

        <!-- Donatur -->
        <div class="mb-3">
            <label class="form-label">Donatur</label>
            <select name="id_donatur" class="form-control" required>
                <?php
                $d = pg_query($koneksi, "SELECT * FROM donatur ORDER BY nama ASC");
                while ($dn = pg_fetch_assoc($d)) :
                ?>
                    <option value="<?= $dn['id']; ?>"
                        <?= $dn['id'] == $data['id_donatur'] ? "selected" : "" ?>>
                        <?= $dn['nama']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Jenis -->
        <div class="mb-3">
            <label class="form-label">Jenis Donasi</label>
            <select name="jenis" class="form-control" id="jenis_select">
                <option value="uang" <?= $jenis == "uang" ? "selected" : "" ?>>Uang</option>
                <option value="barang" <?= $jenis == "barang" ? "selected" : "" ?>>Barang</option>
            </select>
        </div>

        <!-- Form Uang -->
        <div id="form_uang" style="display: <?= $jenis == 'uang' ? 'block' : 'none' ?>;">
            <div class="mb-3">
                <label class="form-label">Nominal</label>
                <input type="number" name="nominal" class="form-control"
                       value="<?= $data_uang['nominal'] ?? '' ?>">
            </div>
        </div>

        <!-- Form Barang -->
        <div id="form_barang" style="display: <?= $jenis == 'barang' ? 'block' : 'none' ?>;">
            <div class="mb-3">
                <label class="form-label">Keterangan Barang</label>
                <input type="text" name="keterangan" class="form-control"
                       value="<?= $data_barang['keterangan'] ?? '' ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Kuantitas</label>
                <input type="number" name="kuantitas" class="form-control"
                       value="<?= $data_barang['kuantitas'] ?? '' ?>">
            </div>
        </div>

        <!-- Tanggal -->
        <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control"
                   value="<?= $data['tanggal'] ?>" required>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Update</button>
        <a href="donasi-list.php" class="btn btn-secondary">Kembali</a>

    </form>
</div>

<script>
document.getElementById("jenis_select").addEventListener("change", function() {
    if (this.value === "uang") {
        document.getElementById("form_uang").style.display = "block";
        document.getElementById("form_barang").style.display = "none";
    } else {
        document.getElementById("form_uang").style.display = "none";
        document.getElementById("form_barang").style.display = "block";
    }
});
</script>

<?php include '../templates/footer.php'; ?>
