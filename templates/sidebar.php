<style>
    #sidebar {
        width: 280px;
        min-height: 100vh;
        background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
        box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        overflow-y: auto;
    }
    
    .sidebar-logo {
        text-align: center;
        padding: 30px 20px;
        background: rgba(81, 81, 198, 0.05);
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .sidebar-logo img {
        width: 80px;
        height: 80px;
        object-fit: contain;
        margin-bottom: 15px;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
    }
    
    .sidebar-logo h3 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 5px;
        color: white;
    }
    
    .sidebar-logo p {
        font-size: 12px;
        color: rgba(255,255,255,0.7);
        margin: 0;
    }
    
    .sidebar-nav {
        padding: 20px 15px;
    }
    
    .nav-item {
        margin-bottom: 8px;
    }
    
    .nav-link {
        display: flex;
        align-items: center;
        padding: 14px 18px;
        border-radius: 12px;
        transition: all 0.3s ease;
        font-size: 15px;
        font-weight: 500;
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        position: relative;
        overflow: hidden;
    }
    
    .nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: #2240c7ff;
        transform: scaleY(0);
        transition: transform 0.3s;
    }
    
    .nav-link:hover {
        background: rgba(102, 126, 234, 0.15);
        color: white;
        transform: translateX(5px);
    }
    
    .nav-link:hover::before {
        transform: scaleY(1);
    }
    
    .nav-link.active {
        background: rgba(102, 126, 234, 0.2);
        color: white;
    }
    
    .nav-link.active::before {
        transform: scaleY(1);
    }
    
    .nav-link iconify-icon {
        margin-right: 12px;
        font-size: 22px;
        flex-shrink: 0;
    }
    
    .nav-divider {
        height: 1px;
        background: rgba(255,255,255,0.1);
        margin: 15px 15px;
    }
    
    /* Active page highlighting */
    <?php 
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>
</style>

<div id="sidebar">
    
    <div class="sidebar-logo">
        <img src="/proyek-basdat/assets/logo_pgc.png" 
             alt="Logo Panti Goceng"
             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Ccircle cx=%2250%22 cy=%2250%22 r=%2245%22 fill=%22%23667eea%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2250%22%3E🏠%3C/text%3E%3C/svg%3E'">
        <h3>Panti Goceng</h3>
        <p>Admin Panel</p>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="list-unstyled">

            <li class="nav-item">
                <a href="/proyek-basdat/dashboard.php" 
                   class="nav-link <?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                    <iconify-icon icon="mdi:view-dashboard"></iconify-icon>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="/proyek-basdat/pages/donatur-list.php" 
                   class="nav-link <?= ($current_page == 'donatur-list.php') ? 'active' : '' ?>">
                    <iconify-icon icon="mdi:account-heart"></iconify-icon>
                    Data Donatur
                </a>
            </li>

            <li class="nav-item">
                <a href="/proyek-basdat/pages/donasi-list.php" 
                   class="nav-link <?= ($current_page == 'donasi-list.php') ? 'active' : '' ?>">
                    <iconify-icon icon="mdi:hand-coin"></iconify-icon>
                    Data Donasi
                </a>
            </li>

            <li class="nav-item">
                <a href="/proyek-basdat/pages/staff-list.php" 
                   class="nav-link <?= ($current_page == 'staff-list.php') ? 'active' : '' ?>">
                    <iconify-icon icon="mdi:account-tie-hat"></iconify-icon>
                    Data Staff
                </a>
            </li>

            <li class="nav-item">
                <a href="/proyek-basdat/pages/event-list.php" 
                   class="nav-link <?= ($current_page == 'event-list.php') ? 'active' : '' ?>">
                    <iconify-icon icon="mdi:calendar-heart"></iconify-icon>
                    Data Event
                </a>
            </li>

            <div class="nav-divider"></div>

        </ul>
    </nav>
    
</div>
