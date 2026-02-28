<?php
// ========================================
// ADMIN: BEST SELLER MANAGER
// ========================================

require_once __DIR__ . '/auth.php';
requireLogin();

// Handle toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        $newValue = (int) ($_POST['value'] ?? 0);

        $stmt = $pdo->prepare("UPDATE products SET is_best_seller = ? WHERE id = ?");
        $stmt->execute([$newValue, $id]);

        $productName = $pdo->prepare("SELECT name FROM products WHERE id = ?");
        $productName->execute([$id]);
        $name = $productName->fetchColumn();

        setFlash('success', $newValue ? "\"$name\" ditandai sebagai Paling Laris!" : "\"$name\" dihapus dari Paling Laris.");
        redirect(BASE_URL . 'admin/best-sellers.php');
    }
}

// Get products grouped
$bestSellers = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_best_seller = 1 ORDER BY p.name")->fetchAll();
$otherProducts = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_best_seller = 0 ORDER BY p.name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Best Seller — Admin Nada Agen Sosis</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>

<body>

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
            <a href="index.php"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            <a href="products.php"><i class="bi bi-box-seam-fill"></i> Produk</a>
            <a href="categories.php"><i class="bi bi-tags-fill"></i> Kategori</a>
            <a href="brands.php"><i class="bi bi-award-fill"></i> Brand</a>
            <a href="best-sellers.php" class="active"><i class="bi bi-fire"></i> Paling Laris</a>
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
                <h1>🔥 Paling Laris</h1>
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

            <!-- Current Best Sellers -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>🔥 Produk Paling Laris (
                        <?= count($bestSellers) ?>)
                    </h2>
                </div>
                <div class="admin-card-body" style="padding:0">
                    <?php if (empty($bestSellers)): ?>
                        <div class="empty-state">
                            <i class="bi bi-fire"></i>
                            <p>Belum ada produk yang ditandai sebagai Paling Laris.</p>
                        </div>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bestSellers as $p): ?>
                                    <tr>
                                        <td><img src="../<?= h($p['img']) ?>" class="product-thumb" alt="<?= h($p['name']) ?>">
                                        </td>
                                        <td>
                                            <strong>
                                                <?= h($p['name']) ?>
                                            </strong>
                                            <br><small style="color:var(--admin-dark-400)">
                                                <?= h($p['brand']) ?>
                                            </small>
                                        </td>
                                        <td><span class="badge badge-primary">
                                                <?= h($p['category_name']) ?>
                                            </span></td>
                                        <td><strong>
                                                <?= h($p['price']) ?>
                                            </strong></td>
                                        <td>
                                            <form method="POST" style="display:inline">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                <input type="hidden" name="value" value="0">
                                                <button type="submit" class="btn-admin btn-admin-danger btn-sm"
                                                    title="Hapus dari Paling Laris">
                                                    <i class="bi bi-fire"></i> Hapus dari Laris
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Other Products -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Produk Lainnya (
                        <?= count($otherProducts) ?>)
                    </h2>
                </div>
                <div class="admin-card-body" style="padding:0">
                    <?php if (empty($otherProducts)): ?>
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>Semua produk sudah ditandai sebagai Paling Laris.</p>
                        </div>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($otherProducts as $p): ?>
                                    <tr>
                                        <td><img src="../<?= h($p['img']) ?>" class="product-thumb" alt="<?= h($p['name']) ?>">
                                        </td>
                                        <td>
                                            <strong>
                                                <?= h($p['name']) ?>
                                            </strong>
                                            <br><small style="color:var(--admin-dark-400)">
                                                <?= h($p['brand']) ?>
                                            </small>
                                        </td>
                                        <td><span class="badge badge-primary">
                                                <?= h($p['category_name']) ?>
                                            </span></td>
                                        <td><strong>
                                                <?= h($p['price']) ?>
                                            </strong></td>
                                        <td>
                                            <form method="POST" style="display:inline">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                                <input type="hidden" name="value" value="1">
                                                <button type="submit" class="btn-admin btn-admin-success btn-sm"
                                                    title="Tandai sebagai Paling Laris">
                                                    <i class="bi bi-fire"></i> Jadikan Laris
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const sToggle = document.getElementById('sidebarToggle');
        const sClose = document.getElementById('sidebarClose');
        function openSidebar() { sidebar.classList.add('mobile-open'); overlay.classList.add('active'); }
        function closeSidebar() { sidebar.classList.remove('mobile-open'); overlay.classList.remove('active'); }
        if (sToggle) sToggle.addEventListener('click', openSidebar);
        if (sClose) sClose.addEventListener('click', closeSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);
    </script>
</body>

</html>