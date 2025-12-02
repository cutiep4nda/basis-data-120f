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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f5f7fa;
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            padding: 40px;
            transition: all 0.3s ease;
            width: 100%;
            overflow-x: hidden;
        }

        /* Header Section */
        .dashboard-header {
            background: white;
            padding: 35px 40px;
            border-radius: 20px;
            box-shadow: 0 2px 15px rgba(34, 64, 199, 0.08);
            margin-bottom: 35px;
            display: flex;
            align-items: center;
            gap: 30px;
            border-left: 5px solid #2240c7;
        }

        .header-logo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header-content h1 {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .header-content p {
            font-size: 15px;
            color: #6b7280;
            line-height: 1.6;
            margin: 0;
        }

        .welcome-text {
            font-size: 16px;
            color: #2240c7;
            font-weight: 600;
            margin-top: 10px;
        }

        /* Stats Cards Container */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 35px;
        }

        .stat-card {
            background: white;
            padding: 28px;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid #f0f0f0;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #2240c7, #667eea);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(34, 64, 199, 0.15);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            background: linear-gradient(135deg, #2240c7 0%, #667eea 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(34, 64, 199, 0.25);
        }

        .stat-body h3 {
            font-size: 15px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: #1a1a2e;
            line-height: 1;
        }

        .stat-value.currency {
            font-size: 28px;
        }

        .stat-value small {
            font-size: 18px;
            color: #6b7280;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .main-content {
                padding: 25px;
            }

            .stats-container {
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                text-align: center;
                padding: 25px;
            }

            .header-content h1 {
                font-size: 24px;
            }

            .header-content p {
                font-size: 14px;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .stat-value {
                font-size: 30px;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .stat-card:nth-child(5) { animation-delay: 0.5s; }
        .stat-card:nth-child(6) { animation-delay: 0.6s; }
    </style>
</head>
<body>
    <div class="main-content">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <img src="assets/logo_pgc.png" alt="Logo Panti Goceng" class="header-logo" 
                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%232240c7%22 width=%22100%22 height=%22100%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2240%22 fill=%22white%22%3E🏠%3C/text%3E%3C/svg%3E'">
            <div class="header-content">
                <h1>Dashboard Admin Komunitas Panti Goceng</h1>
                <p class="welcome-text">Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
                <p>
                    Dirancang oleh lima mahasiswa Ilmu Komputer sebagai Tugas Proyek Mata Kuliah Basis Data untuk memudahkan manajemen data komunitas Panti Goceng. 
                    Panti Goceng adalah komunitas peduli anak yatim yang berkomitmen memberikan kasih sayang, 
                    pendidikan, dan kehidupan yang layak bagi anak-anak panti asuhan melalui program donasi dan kegiatan sosial.
                </p>
                
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-container">
            <!-- Total Cabang -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <iconify-icon icon="mdi:office-building"></iconify-icon>
                    </div>
                </div>
                <div class="stat-body">
                    <h3>Total Cabang</h3>
                    <div class="stat-value">
                        <?php 
                        $q = pg_query($koneksi, "SELECT COUNT(*) FROM cabang");
                        echo number_format(pg_fetch_result($q, 0, 0));
                        ?>
                    </div>
                </div>
            </div>

            <!-- Total Donatur -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <iconify-icon icon="mdi:account-heart"></iconify-icon>
                    </div>
                </div>
                <div class="stat-body">
                    <h3>Total Donatur</h3>
                    <div class="stat-value">
                        <?php 
                        $q = pg_query($koneksi, "SELECT COUNT(*) FROM donatur");
                        echo number_format(pg_fetch_result($q, 0, 0));
                        ?>
                    </div>
                </div>
            </div>

            <!-- Total Donasi -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <iconify-icon icon="mdi:hand-coin"></iconify-icon>
                    </div>
                </div>
                <div class="stat-body">
                    <h3>Total Donasi</h3>
                    <div class="stat-value">
                        <?php 
                        $q = pg_query($koneksi, "SELECT COUNT(*) FROM donasi");
                        echo number_format(pg_fetch_result($q, 0, 0));
                        ?>
                    </div>
                </div>
            </div>

            <!-- Total Donasi Uang -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <iconify-icon icon="mdi:cash-multiple"></iconify-icon>
                    </div>
                </div>
                <div class="stat-body">
                    <h3>Total Donasi Uang</h3>
                    <div class="stat-value currency">
                        <small>Rp</small> <?php 
                        $q = pg_query($koneksi, "SELECT COALESCE(SUM(nominal),0) FROM donasi_uang");
                        echo number_format(pg_fetch_result($q, 0, 0), 0, ',', '.');
                        ?>
                    </div>
                </div>
            </div>

            <!-- Total Staff -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <iconify-icon icon="mdi:account-tie-hat"></iconify-icon>
                    </div>
                </div>
                <div class="stat-body">
                    <h3>Total Staff</h3>
                    <div class="stat-value">
                        <?php 
                        $q = pg_query($koneksi, "SELECT COUNT(*) FROM staff");
                        echo number_format(pg_fetch_result($q, 0, 0));
                        ?>
                    </div>
                </div>
            </div>

            <!-- Total Event -->
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">
                        <iconify-icon icon="mdi:calendar-heart"></iconify-icon>
                    </div>
                </div>
                <div class="stat-body">
                    <h3>Total Event</h3>
                    <div class="stat-value">
                        <?php 
                        $q = pg_query($koneksi, "SELECT COUNT(*) FROM event");
                        echo number_format(pg_fetch_result($q, 0, 0));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'templates/footer.php'; ?>
</body>
</html>
