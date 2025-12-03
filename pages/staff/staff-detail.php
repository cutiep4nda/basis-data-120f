<?php

require '../../config/db.php';
include '../../templates/header.php';
include '../../templates/sidebar.php';

$id_staff = $_GET['id'];

$staff = pg_fetch_assoc(pg_query_params(
    $koneksi,
    "SELECT s.*, c.cabang, d.di_name AS divisi
     FROM staff s
     LEFT JOIN cabang c ON s.id_cabang = c.id
     LEFT JOIN divisi d ON s.id_divisi = d.id
     WHERE s.id = $1",
    [$id_staff]
));

if (!$staff) {
    die("<div class='container mt-4'><h3>Error: Staff tidak ditemukan!</h3></div>");
}

// Ambil daftar event yang diikuti staff
$events = pg_query_params(
    $koneksi,
    "SELECT e.id, e.nama_event,
            CASE
                WHEN ei.id_event IS NOT NULL THEN 'internal'
                WHEN ee.id_event IS NOT NULL THEN 'eksternal'
                ELSE 'N/A'
            END AS tipe_event
     FROM partisipasi p
     JOIN event e ON p.id_event = e.id
     LEFT JOIN event_internal ei ON e.id = ei.id_event
     LEFT JOIN event_eksternal ee ON e.id = ee.id_event
     WHERE p.id_staff = $1",
    [$id_staff]
);
?>

<div class="container mt-4">
    <h2 class="text-center mb-4"><?= htmlspecialchars($staff['nama']) ?></h2>

    <div class="card mb-4">
        <div class="card-header"><strong>Informasi Pribadi</strong></div>
        <div class="card-body">
            <p><strong>Nama:</strong> <?= htmlspecialchars($staff['nama']) ?></p>
            <p><strong>Tempat Lahir:</strong> <?= htmlspecialchars($staff['tempat_lahir'] ?: '-') ?></p>
            <p><strong>Tanggal Lahir:</strong> <?= htmlspecialchars($staff['tanggal_lahir'] ?: '-') ?></p>
            <p><strong>MBTI:</strong> <?= htmlspecialchars($staff['mbti'] ?: '-') ?></p>
            <p><strong>Instansi:</strong> <?= htmlspecialchars($staff['instansi'] ?: '-') ?></p>
            <p><strong>Cabang:</strong> <?= htmlspecialchars($staff['cabang'] ?: '-') ?></p>
            <p><strong>Divisi:</strong> <?= htmlspecialchars($staff['divisi'] ?: '-') ?></p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>Event yang Diikuti</strong></div>
        <div class="card-body">
            <?php if (pg_num_rows($events) == 0): ?>
                <div class="alert alert-warning">Staff ini belum bergabung dengan event apapun.</div>
            <?php else: ?>
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        while ($row = pg_fetch_assoc($events)): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($row['nama_event'] ?: '-') ?></td>
                                <td><?= htmlspecialchars(strtoupper($row['tipe_event'])) ?></td>
                                <td><a href="/p4/pages/event/event-detail.php?id=<?= $row['id']; ?>" class="btn btn-info">
                                        Lihat Detail
                                    </a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="text-center mb-4">
        <a href="staff-list.php" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<?php include '../../templates/footer.php'; ?>