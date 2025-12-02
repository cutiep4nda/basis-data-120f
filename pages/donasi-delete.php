<?php

require '../function.php';

$id = $_GET['id'];

pg_query_params($koneksi, "DELETE FROM donasi WHERE id_donasi = $1", [$id]);

header("Location: donasi-list.php");
exit;
?>