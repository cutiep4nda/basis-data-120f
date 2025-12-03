<?php
require '../../config/db.php';

$id_tempat = $_POST['id_tempat'];
$id_event = $_POST['id_event'];

$jml_anak = $_POST['jml_anak'];
$min_usia = $_POST['min_usia'];
$max_usia = $_POST['max_usia'];
$min_pendidikan = $_POST['min_pendidikan'];
$max_pendidikan = $_POST['max_pendidikan'];

$result = pg_query_params(
    $koneksi,
    "UPDATE tempat_panti 
     SET jml_anak = $1,
         min_usia = $2,
         max_usia = $3,
         min_pendidikan = $4,
         max_pendidikan = $5
     WHERE id_tempat = $6",
    [$jml_anak, $min_usia, $max_usia, $min_pendidikan, $max_pendidikan, $id_tempat]
);

if ($result) {
    header("Location: event-detail.php?id=" . $id_event . "&msg=update_success");
    exit;
} else {
    echo "<h3>❌ Gagal mengupdate data tempat!</h3>";
}
