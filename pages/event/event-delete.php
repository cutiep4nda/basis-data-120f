<?php

require '../../config/db.php';

$id = $_GET['id'];

pg_query($koneksi, "BEGIN");

$res = pg_query_params($koneksi, "DELETE FROM partisipasi WHERE id_event = $1", [$id]);
if ($res) {
    $res = pg_query_params($koneksi, "DELETE FROM event WHERE id = $1", [$id]);
    if ($res) {
        pg_query($koneksi, "COMMIT");
    } else {
        pg_query($koneksi, "ROLLBACK");
    }
} else {
    pg_query($koneksi, "ROLLBACK");
}
header("Location: event-list.php");
exit;
?>