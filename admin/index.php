<?php
// ========================================
// ADMIN DASHBOARD
// ========================================

require_once __DIR__ . '/auth.php';
requireLogin();

// Get stats
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalBestSellers = $pdo->query("SELECT COUNT(*) FROM products WHERE is_best_seller = 1")->fetchColumn();
$totalNewProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE is_new = 1")->fetchColumn();

// Get recent products
$recentProducts = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Admin Nada Agen Sosis</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- SIDEBAR -->
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="admin-sidebar" id="adminSidebar">
        <button class="sidebar-close" id="sidebarClose"><i class="bi bi-x-lg"></i></button>
        <div class="sidebar-brand">
            <img src="../img/logo.png" alt="Logo">
            <h2>Nada <span>Admin</span></h2>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">Menu Utama</div>
            <a href="index.php" class="active"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a href="products.php"><i class="bi bi-box-seam-fill"></i> Produk</a>
            <a href="categories.php"><i class="bi bi-tags-fill"></i> Kategori</a>
            <a href="best-sellers.php"><i class="bi bi-fire"></i> Paling Laris</a>
            <div class="nav-section">Lainnya</div>
            <a href="../index.html" target="_blank"><i class="bi bi-globe2"></i> Lihat Website</a>
        </nav>
        <div class="sidebar-footer">
            <a href="logout.php"><i class="bi bi-box-arrow-left"></i> Keluar</a>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="admin-main">
        <div class="admin-topbar">
            <div style="display:flex;align-items:center;gap:12px">
                <button class="topbar-hamburger" id="sidebarToggle"><i class="bi bi-list"></i></button>
                <h1>Dashboard</h1>
            </div>
            <div class="topbar-right">
                <i class="bi bi-person-circle"></i>
                <span>Admin
                    <?= h($_SESSION['admin_username'] ?? '') ?>
                </span>
            </div>
        </div>

        <div class="admin-content">
            <?php $flash = getFlash();
            if ($flash): ?>
                <div class="flash-msg flash-<?= $flash['type'] ?>">
                    <i
                        class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill' ?>"></i>
                    <?= h($flash['message']) ?>
                </div>
            <?php endif; ?>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-box-seam-fill"></i></div>
                    <div class="stat-info">
                        <h3>
                            <?= $totalProducts ?>
                        </h3>
                        <p>Total Produk</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="bi bi-tags-fill"></i></div>
                    <div class="stat-info">
                        <h3>
                            <?= $totalCategories ?>
                        </h3>
                        <p>Kategori</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="bi bi-fire"></i></div>
                    <div class="stat-info">
                        <h3>
                            <?= $totalBestSellers ?>
                        </h3>
                        <p>Paling Laris</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-stars"></i></div>
                    <div class="stat-info">
                        <h3>
                            <?= $totalNewProducts ?>
                        </h3>
                        <p>Produk Baru</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="admin-card" style="margin-bottom:24px">
                <div class="admin-card-header">
                    <h2><i class="bi bi-lightning-charge-fill" style="color:var(--admin-warning)"></i> Aksi Cepat</h2>
                </div>
                <div class="admin-card-body" style="display:flex;gap:12px;flex-wrap:wrap">
                    <a href="products.php?action=add" class="btn-admin btn-admin-primary"><i class="bi bi-plus-lg"></i>
                        Tambah Produk</a>
                    <a href="categories.php" class="btn-admin btn-admin-outline"><i class="bi bi-tags"></i> Kelola
                        Kategori</a>
                    <a href="best-sellers.php" class="btn-admin btn-admin-outline"><i class="bi bi-fire"></i> Kelola
                        Best Seller</a>
                </div>
            </div>

            <!-- Recent Products -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Produk Terbaru</h2>
                    <a href="products.php" class="btn-admin btn-admin-outline btn-sm">Lihat Semua</a>
                </div>
                <div class="admin-card-body" style="padding:0">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentProducts as $p): ?>
                                <tr>
                                    <td><img src="../<?= h($p['img']) ?>" class="product-thumb" alt="<?= h($p['name']) ?>">
                                    </td>
                                    <td><strong>
                                            <?= h($p['name']) ?>
                                        </strong><br><small style="color:var(--admin-dark-400)">
                                            <?= h($p['brand']) ?>
                                        </small></td>
                                    <td><span class="badge badge-primary">
                                            <?= h($p['category_name']) ?>
                                        </span></td>
                                    <td><strong>
                                            <?= h($p['price']) ?>
                                        </strong></td>
                                    <td>
                                        <?php if ($p['is_best_seller']): ?><span class="badge badge-warning">🔥 Laris</span>
                                        <?php endif; ?>
                                        <?php if ($p['is_new']): ?><span class="badge badge-info">✨ Baru</span>
                                        <?php endif; ?>
                                        <?php if ($p['halal']): ?><span class="badge badge-success">Halal</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script>
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggle = document.getElementById('sidebarToggle');
        const close = document.getElementById('sidebarClose');
        function openSidebar() { sidebar.classList.add('mobile-open'); overlay.classList.add('active'); }
        function closeSidebar() { sidebar.classList.remove('mobile-open'); overlay.classList.remove('active'); }
        if (toggle) toggle.addEventListener('click', openSidebar);
        if (close) close.addEventListener('click', closeSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);
    </script>
</body>

</html>