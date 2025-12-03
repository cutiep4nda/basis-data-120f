<?php

require '../../config/db.php';

$id = $_GET['id'];

pg_query_params($koneksi, "DELETE FROM donatur WHERE id_donatur = $1", [$id]);

header("Location: donatur-list.php");
exit;
?>