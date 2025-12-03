<?php

require '../../config/db.php';
include '../../templates/header.php';
include '../../templates/sidebar.php';

$success = "";
$error = "";

if (isset($_POST['submit'])) {

    //print_r($_POST);
    //exit;

    $nama_event = $_POST['nama_event'];
    $jenis_event = $_POST['jenis_event'];
    $tema = $_POST['deskripsi'];
    $id_tempat = $_POST['id_tempat'];

    pg_query($koneksi, "BEGIN");

    // =============== HANDLE TAMBAH TEMPAT BARU ===============
    if ($id_tempat == "baru") {

        $jenis_tempat = $_POST['jenis_tempat'];
        $nama_tempat = $_POST['nama_tempat'];

        // insert ke tabel tempat
        $q_tempat = pg_query($koneksi, "INSERT INTO tempat DEFAULT VALUES RETURNING id");
        $new_tempat = pg_fetch_assoc($q_tempat);
        $id_tempat = $new_tempat['id'];

        if ($jenis_tempat == "panti") {

            // HANYA nama_panti yang diisi → kolom lain follow DEFAULT
            pg_query_params(
                $koneksi,
                "INSERT INTO tempat_panti (id_tempat, nama_panti) VALUES ($1, $2)",
                [$id_tempat, $nama_tempat]
            );

        } else { // Tempat umum

            pg_query_params(
                $koneksi,
                "INSERT INTO tempat_umum (id_tempat, ruang) VALUES ($1, $2)",
                [$id_tempat, $nama_tempat]
            );
        }
    }

    // =============== INSERT EVENT (UMUM) =====================
    $query_event = "INSERT INTO event (nama_event, tema_event, id_tempat)
                    VALUES ($1, $2, $3) RETURNING id";

    $result_event = pg_query_params($koneksi, $query_event, [
        $nama_event,
        $tema,
        $id_tempat
    ]);

    if (!$result_event) {
        pg_query($koneksi, "ROLLBACK");
        $error = "Gagal menambah event!";
    } else {

        $row_event = pg_fetch_assoc($result_event);
        $id_event = $row_event['id'];

        // =============== INSERT DETAIL BERDASARKAN JENIS =====================
        if ($jenis_event == "internal") {

            $tanggal = $_POST['tanggal'];
            $query_internal = "INSERT INTO event_internal (id_event, tanggal)
                               VALUES ($1, $2)";
            $ress = pg_query_params($koneksi, $query_internal, [$id_event, $tanggal]);

        } else { // eksternal

            $tanggal_mulai = $_POST['tanggal_mulai'];
            $tanggal_selesai = $_POST['tanggal_selesai'];
            $deskripsi_tambahan = $_POST['deskripsi_tambahan'];

            $query_eksternal = "INSERT INTO event_eksternal (id_event, tanggal_mulai, tanggal_selesai, deskripsi)
                                VALUES ($1, $2, $3, $4)";
            $ress = pg_query_params($koneksi, $query_eksternal, [
                $id_event,
                $tanggal_mulai,
                $tanggal_selesai,
                $deskripsi_tambahan
            ]);
        }

        if ($ress) {
            pg_query($koneksi, "COMMIT");
            $success = "Event berhasil ditambahkan!";
        } else {
            pg_query($koneksi, "ROLLBACK");
            $error = "Event gagal ditambahkan!";
        }
    }
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
            <label class="form-label">Jenis Event</label>
            <select name="jenis_event" id="jenis_event" class="form-control" required>
                <option value="">-- pilih jenis event --</option>
                <option value="internal">Internal</option>
                <option value="eksternal">Eksternal</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tempat Event</label>
            <select name="id_tempat" id="id_tempat" class="form-control" required>
                <option value="">-- pilih tempat --</option>

                <?php
                // Ambil tempat panti
                $tp = pg_query($koneksi, "SELECT t.id, p.nama_panti AS nama FROM tempat t 
                    JOIN tempat_panti p ON t.id = p.id_tempat ORDER BY nama ASC");
                while ($row = pg_fetch_assoc($tp)) {
                    echo '<option value="' . $row['id'] . '">' . $row['nama'] . ' (Panti)</option>';
                }

                // Ambil tempat umum
                $tu = pg_query($koneksi, "SELECT t.id, u.ruang AS nama FROM tempat t 
                    JOIN tempat_umum u ON t.id = u.id_tempat ORDER BY nama ASC");
                while ($row = pg_fetch_assoc($tu)) {
                    echo '<option value="' . $row['id'] . '">' . $row['nama'] . ' (Umum)</option>';
                }
                ?>
                <option value="baru">➕ Tambahkan tempat baru</option>
            </select>
        </div>

        <!-- FORM TAMBAH TEMPAT (HIDDEN) -->
        <div id="form_tempat_baru" style="display:none;">
            <hr>
            <h5>Tambah Tempat Baru</h5>

            <div class="mb-3">
                <label>Jenis Tempat</label>
                <select id="jenis_tempat" name="jenis_tempat" class="form-control">
                    <option value="panti">Panti</option>
                    <option value="umum">Umum</option>
                </select>
            </div>

            <div class="mb-3">
                <label id="label_nama_tempat">Nama Panti</label>
                <input type="text" name="nama_tempat" class="form-control" required>
            </div>
        </div>

        <script>
            document.getElementById("id_tempat").addEventListener("change", function () {
                document.getElementById("form_tempat_baru").style.display =
                    this.value === "baru" ? "block" : "none";
            });

            document.getElementById("jenis_tempat").addEventListener("change", function () {
                const label = document.getElementById("label_nama_tempat");
                label.innerText = this.value === "panti"
                    ? "Nama Panti"
                    : "Ruang / Nama Tempat";
            });
        </script>

        <!-- FORM DETAIL EVENT -->
        <div id="form_eksternal" style="display:none;">
            <div class="mb-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi_tambahan" class="form-control"></textarea>
            </div>
        </div>

        <div id="form_internal" style="display:none;">
            <div class="mb-3">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control">
            </div>
        </div>

        <script>
            document.getElementById("jenis_event").addEventListener("change", function () {
                if (this.value === "eksternal") {
                    document.getElementById("form_eksternal").style.display = "block";
                    document.getElementById("form_internal").style.display = "none";
                } else {
                    document.getElementById("form_internal").style.display = "block";
                    document.getElementById("form_eksternal").style.display = "none";
                }
            });
        </script>

        <div class="mb-3">
            <label class="form-label">Tema</label>
            <textarea name="deskripsi" class="form-control"></textarea>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        <a href="event-list.php" class="btn btn-secondary">Kembali</a>

    </form>

</div>

<?php include '../../templates/footer.php'; ?>