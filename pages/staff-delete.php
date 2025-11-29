<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: ../index.php");
    exit;
}

require '../function.php';

$id = $_GET['id'];

pg_query_params($koneksi, "DELETE FROM staff WHERE id_staff = $1", [$id]);

header("Location: staff-list.php");
exit;
?>
