<?php
// ========================================
// ADMIN: BRAND MANAGEMENT
// ========================================

require_once __DIR__ . '/auth.php';
requireLogin();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    // --- ADD BRAND ---
    if ($postAction === 'add') {
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');

        if (empty($name)) {
            setFlash('error', 'Nama brand wajib diisi.');
            redirect(BASE_URL . 'admin/brands.php');
        }

        // Check if brand already exists
        $check = $pdo->prepare("SELECT COUNT(*) FROM brands WHERE name = ?");
        $check->execute([$name]);
        if ($check->fetchColumn() > 0) {
            setFlash('error', "Brand \"$name\" sudah ada.");
            redirect(BASE_URL . 'admin/brands.php');
        }

        $stmt = $pdo->prepare("INSERT INTO brands (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $desc]);

        setFlash('success', "Brand \"$name\" berhasil ditambahkan!");
        redirect(BASE_URL . 'admin/brands.php');
    }

    // --- EDIT BRAND ---
    if ($postAction === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');

        if (empty($name)) {
            setFlash('error', 'Nama brand wajib diisi.');
            redirect(BASE_URL . 'admin/brands.php');
        }

        // Check if brand name is used by another brand
        $check = $pdo->prepare("SELECT COUNT(*) FROM brands WHERE name = ? AND id != ?");
        $check->execute([$name, $id]);
        if ($check->fetchColumn() > 0) {
            setFlash('error', "Brand \"$name\" sudah ada.");
            redirect(BASE_URL . 'admin/brands.php');
        }

        // Get old brand name for updating products
        $oldBrand = $pdo->prepare("SELECT name FROM brands WHERE id = ?");
        $oldBrand->execute([$id]);
        $oldName = $oldBrand->fetchColumn();

        $stmt = $pdo->prepare("UPDATE brands SET name=?, description=? WHERE id=?");
        $stmt->execute([$name, $desc, $id]);

        // Also update existing products that have this brand name in the text field
        if ($oldName && $oldName !== $name) {
            $updateProducts = $pdo->prepare("UPDATE products SET brand = ? WHERE brand_id = ?");
            $updateProducts->execute([$name, $id]);
        }

        setFlash('success', "Brand \"$name\" berhasil diperbarui!");
        redirect(BASE_URL . 'admin/brands.php');
    }

    // --- DELETE BRAND ---
    if ($postAction === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        // Check if brand has products
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE brand_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            setFlash('error', "Brand tidak bisa dihapus karena masih memiliki $count produk. Pindahkan atau hapus produk terlebih dahulu.");
        } else {
            $stmt = $pdo->prepare("SELECT name FROM brands WHERE id = ?");
            $stmt->execute([$id]);
            $brandName = $stmt->fetchColumn();

            $stmt = $pdo->prepare("DELETE FROM brands WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', "Brand \"$brandName\" berhasil dihapus!");
        }
        redirect(BASE_URL . 'admin/brands.php');
    }
}

// Determine action
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Get edit data
$editBrand = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM brands WHERE id = ?");
    $stmt->execute([$id]);
    $editBrand = $stmt->fetch();
    if (!$editBrand) {
        setFlash('error', 'Brand tidak ditemukan.');
        redirect(BASE_URL . 'admin/brands.php');
    }
}

// Get all brands with product count
$brands = $pdo->query("SELECT b.*, (SELECT COUNT(*) FROM products WHERE brand_id = b.id) as product_count FROM brands b ORDER BY b.name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand — Admin Nada Agen Sosis</title>
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
            <a href="brands.php" class="active"><i class="bi bi-award-fill"></i> Brand</a>
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
                <h1>Manajemen Brand</h1>
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

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px" class="cat-grid">
                <!-- ADD / EDIT FORM -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2><i class="bi bi-<?= $editBrand ? 'pencil-square' : 'plus-circle' ?>"></i>
                            <?= $editBrand ? 'Edit Brand' : 'Tambah Brand' ?>
                        </h2>
                        <?php if ($editBrand): ?>
                            <a href="brands.php" class="btn-admin btn-admin-outline btn-sm"><i class="bi bi-x-lg"></i>
                                Batal</a>
                        <?php endif; ?>
                    </div>
                    <div class="admin-card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="<?= $editBrand ? 'edit' : 'add' ?>">
                            <?php if ($editBrand): ?>
                                <input type="hidden" name="id" value="<?= $editBrand['id'] ?>">
                            <?php endif; ?>

                            <div class="form-group">
                                <label>Nama Brand <span class="required">*</span></label>
                                <input type="text" name="name" class="form-control" required
                                    value="<?= h($editBrand['name'] ?? '') ?>" placeholder="Contoh: Kanzler">
                            </div>

                            <div class="form-group">
                                <label>Deskripsi</label>
                                <input type="text" name="description" class="form-control"
                                    value="<?= h($editBrand['description'] ?? '') ?>"
                                    placeholder="Deskripsi singkat brand">
                            </div>

                            <button type="submit" class="btn-admin btn-admin-primary">
                                <i class="bi bi-<?= $editBrand ? 'check-lg' : 'plus-lg' ?>"></i>
                                <?= $editBrand ? 'Simpan Perubahan' : 'Tambah Brand' ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- BRAND LIST -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>
                            <?= count($brands) ?> Brand
                        </h2>
                    </div>
                    <div class="admin-card-body" style="padding:0">
                        <?php if (empty($brands)): ?>
                            <div class="empty-state">
                                <i class="bi bi-award"></i>
                                <p>Belum ada brand.</p>
                            </div>
                        <?php else: ?>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Nama Brand</th>
                                        <th>Deskripsi</th>
                                        <th>Produk</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($brands as $b): ?>
                                        <tr>
                                            <td>
                                                <strong>
                                                    <?= h($b['name']) ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <small style="color:var(--admin-dark-400)">
                                                    <?= h($b['description']) ?: '-' ?>
                                                </small>
                                            </td>
                                            <td><span class="badge badge-primary">
                                                    <?= $b['product_count'] ?> produk
                                                </span></td>
                                            <td>
                                                <div style="display:flex;gap:6px">
                                                    <a href="brands.php?action=edit&id=<?= $b['id'] ?>"
                                                        class="btn-admin btn-admin-outline btn-icon" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <?php if ($b['product_count'] == 0): ?>
                                                        <button class="btn-admin btn-admin-danger btn-icon"
                                                            onclick="confirmDeleteBrand(<?= $b['id'] ?>, '<?= h(addslashes($b['name'])) ?>')"
                                                            title="Hapus">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn-admin btn-admin-outline btn-icon" disabled
                                                            title="Tidak bisa dihapus (masih ada produk)"
                                                            style="opacity:.4;cursor:not-allowed">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Delete Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <i class="bi bi-exclamation-triangle-fill" style="font-size:3rem;color:var(--admin-danger)"></i>
            <h3>Hapus Brand?</h3>
            <p id="deleteMsg">Apakah Anda yakin ingin menghapus brand ini?</p>
            <div class="modal-actions">
                <button class="btn-admin btn-admin-outline"
                    onclick="document.getElementById('deleteModal').classList.remove('active')">Batal</button>
                <form method="POST" id="deleteForm" style="display:inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteId">
                    <button type="submit" class="btn-admin btn-admin-danger"><i class="bi bi-trash3"></i> Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function confirmDeleteBrand(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteMsg').textContent = `Apakah Anda yakin ingin menghapus brand "${name}"?`;
            document.getElementById('deleteModal').classList.add('active');
        }
        document.getElementById('deleteModal').addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('active');
        });
    </script>
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