<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$koneksi = pg_connect("host=localhost port=5432 dbname=pgc2_database user=postgres password=admin");

if (!$koneksi) {
    echo "Koneksi Gagal";
} else {
    echo "Koneksi BERHASIL";
}
?>
