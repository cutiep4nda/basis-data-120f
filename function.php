<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$port = "5432";
$dbname = "pgc2_database";
$user = "postgres";
$password = "admin";

$koneksi = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$koneksi) {
    die("Koneksi ke PostgreSQL gagal!");
}
?>
