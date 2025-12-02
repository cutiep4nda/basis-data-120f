<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$port = "5432";
$dbname = "pgc_database";
$user = "postgres";
$password = "root";

$koneksi = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$koneksi) {
    die("Koneksi ke PostgreSQL gagal!");
}
?>