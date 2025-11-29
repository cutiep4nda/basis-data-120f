<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: index.php");
    exit;
}

require 'function.php';
include 'templates/header.php';
include 'templates/sidebar.php';
?>

<div class="container mt-4">
    <h2>Dashboard Admin</h2>
    <p>Selamat datang, <?php echo $_SESSION['username']; ?>!</p>

    <div class="row">

        <!-- Total Donatur -->
        <div class="col-md-3">
            <div class="card p-3 bg-primary text-white">
                <h4>Total Donatur</h4>
                <h2>
                    <?php 
                    $q = pg_query($koneksi, "SELECT COUNT(*) FROM donatur");
                    echo pg_fetch_result($q, 0, 0);
                    ?>
                </h2>
            </div>
        </div>

        <!-- Total Donasi (Transaksi Donasi) -->
        <div class="col-md-3">
            <div class="card p-3 bg-success text-white">
                <h4>Total Donasi</h4>
                <h2>
                    <?php 
                    $q = pg_query($koneksi, "SELECT COUNT(*) FROM donasi");
                    echo pg_fetch_result($q, 0, 0);
                    ?>
                </h2>
            </div>
        </div>

        <!-- Total Staff -->
        <div class="col-md-3">
            <div class="card p-3 bg-warning text-white">
                <h4>Total Staff</h4>
                <h2>
                    <?php 
                    $q = pg_query($koneksi, "SELECT COUNT(*) FROM staff");
                    echo pg_fetch_result($q, 0, 0);
                    ?>
                </h2>
            </div>
        </div>

        <!-- Total Event -->
        <div class="col-md-3">
            <div class="card p-3 bg-info text-white">
                <h4>Total Event</h4>
                <h2>
                    <?php 
                    $q = pg_query($koneksi, "SELECT COUNT(*) FROM event");
                    echo pg_fetch_result($q, 0, 0);
                    ?>
                </h2>
            </div>
        </div>

        <!-- Total Donasi Uang -->
        <div class="col-md-3 mt-3">
            <div class="card p-3 bg-dark text-white">
                <h4>Total Donasi Uang (Rp)</h4>
                <h2>
                    <?php 
                    $q = pg_query($koneksi, "SELECT COALESCE(SUM(nominal),0) FROM donasi_uang");
                    echo number_format(pg_fetch_result($q, 0, 0), 0, ',', '.');
                    ?>
                </h2>
            </div>
        </div>

        <!-- Total Donasi Barang -->
        <div class="col-md-3 mt-3">
            <div class="card p-3 bg-secondary text-white">
                <h4>Total Donasi Barang</h4>
                <h2>
                    <?php 
                    $q = pg_query($koneksi, "SELECT COALESCE(SUM(kuantitas), 0) FROM donasi_barang");
                    echo pg_fetch_result($q, 0, 0);
                    ?>
                </h2>
            </div>
        </div>

    </div>
</div>

<?php include 'templates/footer.php'; ?>
