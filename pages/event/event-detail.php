<?php
require '../../config/db.php';
include '../../templates/header.php';
include '../../templates/sidebar.php';

$id = $_GET['id'];

// Ambil data event
$event = pg_fetch_assoc(pg_query_params(
    $koneksi,
    "SELECT e.*, 
            tu.ruang,
            tp.nama_panti,
            tp.jml_anak, tp.min_usia, tp.max_usia, tp.min_pendidikan, tp.max_pendidikan,
            ei.tanggal AS tanggal_internal,
            ee.tanggal_mulai,
            ee.tanggal_selesai,
            ee.deskripsi AS deskripsi_eksternal,
            CASE
                WHEN ei.id_event IS NOT NULL THEN 'internal'
                WHEN ee.id_event IS NOT NULL THEN 'eksternal'
                ELSE 'N/A'
            END AS tipe_event
     FROM event e
     LEFT JOIN tempat_umum tu ON e.id_tempat = tu.id_tempat
     LEFT JOIN tempat_panti tp ON e.id_tempat = tp.id_tempat
     LEFT JOIN event_internal ei ON e.id = ei.id_event
     LEFT JOIN event_eksternal ee ON e.id = ee.id_event
     WHERE e.id = $1",
    [$id]
));

if (!$event) {
    echo "<div class='container mt-4'>
            <div class='alert alert-danger'>Event tidak ditemukan.</div>
          </div>";
    include '../../templates/footer.php';
    exit;
}

// Ambil daftar staff
$staff = pg_query_params(
    $koneksi,
    "SELECT 
         s.nama, 
         d.di_name AS jabatan, 
         s.instansi, 
         c.cabang
     FROM partisipasi p
     JOIN staff s ON p.id_staff = s.id
     JOIN cabang c ON s.id_cabang = c.id
     JOIN divisi d ON s.id_divisi = d.id
     WHERE p.id_event = $1",
    [$id]
);

$ketua = pg_fetch_assoc($staff);
?>
<div class="container mt-4">

    <h2 class="text-center mb-1"><?= htmlspecialchars($event['tema_event']) ?></h2>
    <p class="text-center text-muted mb-4">
        <strong><?= strtoupper($event["tipe_event"]) ?></strong>
    </p>

    <!-- Informasi Event -->
    <div class="card mb-4">
        <div class="card-header"><strong>Informasi</strong></div>
        <div class="card-body">

            <p><strong>Nama Event:</strong> <?= htmlspecialchars($event['nama_event']) ?></p>
            <p><strong>Tema:</strong> <?= htmlspecialchars($event['tema_event']) ?></p>

            <?php if ($event["tipe_event"] == "internal"): ?>
                <p><strong>Tanggal:</strong> <?= $event["tanggal_internal"] ?: '-' ?></p>

            <?php elseif ($event["tipe_event"] == "eksternal"): ?>
                <p><strong>Tanggal Mulai:</strong> <?= $event["tanggal_mulai"] ?></p>
                <p><strong>Tanggal Selesai:</strong> <?= $event["tanggal_selesai"] ?></p>

                <?php
                if ($event["tanggal_mulai"] && $event["tanggal_selesai"]) {
                    $tgl_mulai = new DateTime($event["tanggal_mulai"]);
                    $tgl_selesai = new DateTime($event["tanggal_selesai"]);
                    $durasi = $tgl_selesai->diff($tgl_mulai)->days + 1;
                } else {
                    $durasi = '-';
                }
                ?>
                <p><strong>Durasi:</strong> <?= is_numeric($durasi) ? $durasi . ' hari' : '-' ?></p>

                <p><strong>Deskripsi:</strong><br>
                    <?= htmlspecialchars($event["deskripsi_eksternal"] ?: "-") ?>
                </p>
            <?php endif; ?>

        </div>
    </div>


    <!-- Form Edit Tempat Eksternal -->
    <!-- Form Edit Tempat Eksternal -->
    <?php if ($event["tipe_event"] == "eksternal"): ?>
        <div class="card mb-4">
            <div class="card-header"><strong>Edit Informasi Tempat Eksternal</strong></div>
            <div class="card-body">

                <form action="event-update-te.php" method="POST">
                    <input type="hidden" name="id_tempat" value="<?= $event['id_tempat'] ?>">
                    <input type="hidden" name="id_event" value="<?= $event['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label">Jumlah Anak</label>
                        <input type="number" class="form-control" name="jml_anak"
                            value="<?= htmlspecialchars($event['jml_anak']) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Minimum Usia</label>
                            <input type="number" class="form-control" name="min_usia"
                                value="<?= htmlspecialchars($event['min_usia']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Maksimum Usia</label>
                            <input type="number" class="form-control" name="max_usia"
                                value="<?= htmlspecialchars($event['max_usia']) ?>" required>
                        </div>
                    </div>

                    <?php
                    $options = [
                        'BELUM SEKOLAH',
                        'TK',
                        'SD',
                        'SMP',
                        'SMA',
                        'S1',
                        'S2',
                        'S3'
                    ];
                    ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Minimum Pendidikan</label>
                            <select class="form-select" name="min_pendidikan" required>
                                <?php foreach ($options as $op): ?>
                                    <option value="<?= $op ?>" <?= $op == $event['min_pendidikan'] ? "selected" : "" ?>>
                                        <?= $op ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Maksimum Pendidikan</label>
                            <select class="form-select" name="max_pendidikan" required>
                                <?php foreach ($options as $op): ?>
                                    <option value="<?= $op ?>" <?= $op == $event['max_pendidikan'] ? "selected" : "" ?>>
                                        <?= $op ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    <?php endif; ?>



    <!-- Tabel Staff -->
    <div class="card mb-4">
        <div class="card-header"><strong>Kepanitiaan</strong></div>
        <div class="card-body">
            <?php if (!$ketua): ?>
                <div class="alert alert-warning">Belum ada staff yang ditugaskan.</div>
            <?php else: ?>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Divisi</th>
                            <th>Instansi</th>
                            <th>Cabang</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <tr class="table-primary">
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($ketua['nama']) ?></td>
                            <td><?= htmlspecialchars($ketua['jabatan']) ?></td>
                            <td><?= htmlspecialchars($ketua['instansi']) ?></td>
                            <td><?= htmlspecialchars($ketua['cabang']) ?></td>
                            <td><strong>Ketua Pelaksana</strong></td>
                        </tr>
                        <?php
                        if (pg_num_rows($staff) > 1) {
                            $is_first = true;
                            pg_result_seek($staff, 0);
                            while ($row = pg_fetch_assoc($staff)) {
                                if ($is_first) {
                                    $is_first = false;
                                    continue;
                                }
                                ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= htmlspecialchars($row['jabatan']) ?></td>
                                    <td><?= htmlspecialchars($row['instansi']) ?></td>
                                    <td><?= htmlspecialchars($row['cabang']) ?></td>
                                    <td>-</td>
                                </tr>
                            <?php }
                        } ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="text-center mb-4">
        <a href="event-list.php" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>