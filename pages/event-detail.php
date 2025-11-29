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
$event = pg_fetch_assoc(pg_query_params(
    $koneksi,
    "SELECT e.*, 
            tu.ruang,
            tp.nama_panti
     FROM event e
     LEFT JOIN tempat_umum tu ON e.id_tempat = tu.id_tempat
     LEFT JOIN tempat_panti tp ON e.id_tempat = tp.id_tempat
     WHERE e.id = $1",
    [$id]
));

if (!$event) {
    echo "<div class='container mt-4'>
            <div class='alert alert-danger'>Event tidak ditemukan.</div>
          </div>";
    include '../templates/footer.php';
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
?>

<div class="container mt-4">
    <h3>Detail Event: <b><?= $event['tema_event'] ?></b></h3>

    <p><strong>ID Event:</strong> <?= $event["id"] ?></p>

    <p><strong>Tempat:</strong>
        <?php if ($event["ruang"]): ?>
            <?= $event["ruang"] ?>
        <?php elseif ($event["nama_panti"]): ?>
            <?= $event["nama_panti"] ?>
        <?php else: ?>
            Tidak ada informasi tempat.
        <?php endif; ?>
    </p>

    <hr>

    <h4>Staff yang Bertugas</h4>

    <?php if (!$staff || pg_num_rows($staff) == 0): ?>
        <div class="alert alert-warning">Belum ada staff yang ditugaskan.</div>
    <?php else: ?>
        <ul>
            <?php while ($row = pg_fetch_assoc($staff)): ?>
                <li style="margin-bottom: 8px;">
                    <b><?= $row['nama'] ?></b>
                    <br>Jabatan: <?= $row['jabatan'] ?>
                    <br>Instansi: <?= $row['instansi'] ?>
                    <br>Cabang: <?= $row['cabang'] ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>

    <a href="event-list.php" class="btn btn-secondary mt-2">Kembali</a>
</div>

<?php include '../templates/footer.php'; ?>
