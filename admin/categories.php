<?php
// ========================================
// ADMIN: CATEGORY MANAGEMENT
// ========================================

require_once __DIR__ . '/auth.php';
requireLogin();

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    // --- ADD CATEGORY ---
    if ($postAction === 'add') {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $icon = trim($_POST['icon'] ?? '');
        $desc = trim($_POST['description'] ?? '');

        if (empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
        }

        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, icon, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $icon, $desc]);

        setFlash('success', "Kategori \"$name\" berhasil ditambahkan!");
        redirect(BASE_URL . 'admin/categories.php');
    }

    // --- EDIT CATEGORY ---
    if ($postAction === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $icon = trim($_POST['icon'] ?? '');
        $desc = trim($_POST['description'] ?? '');

        if (empty($slug)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
        }

        $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=?, icon=?, description=? WHERE id=?");
        $stmt->execute([$name, $slug, $icon, $desc, $id]);

        setFlash('success', "Kategori \"$name\" berhasil diperbarui!");
        redirect(BASE_URL . 'admin/categories.php');
    }

    // --- DELETE CATEGORY ---
    if ($postAction === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        // Check if category has products
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            setFlash('error', "Kategori tidak bisa dihapus karena masih memiliki $count produk. Pindahkan atau hapus produk terlebih dahulu.");
        } else {
            $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $catName = $stmt->fetchColumn();

            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            setFlash('success', "Kategori \"$catName\" berhasil dihapus!");
        }
        redirect(BASE_URL . 'admin/categories.php');
    }
}

// Determine action
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Get edit data
$editCategory = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $editCategory = $stmt->fetch();
    if (!$editCategory) {
        setFlash('error', 'Kategori tidak ditemukan.');
        redirect(BASE_URL . 'admin/categories.php');
    }
}

// Get all categories with product count
$categories = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count FROM categories c ORDER BY c.name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori — Admin Nada Agen Sosis</title>
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
            <a href="categories.php" class="active"><i class="bi bi-tags-fill"></i> Kategori</a>
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
                <h1>Manajemen Kategori</h1>
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
                        <h2><i class="bi bi-<?= $editCategory ? 'pencil-square' : 'plus-circle' ?>"></i>
                            <?= $editCategory ? 'Edit Kategori' : 'Tambah Kategori' ?>
                        </h2>
                        <?php if ($editCategory): ?>
                            <a href="categories.php" class="btn-admin btn-admin-outline btn-sm"><i class="bi bi-x-lg"></i>
                                Batal</a>
                        <?php endif; ?>
                    </div>
                    <div class="admin-card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="<?= $editCategory ? 'edit' : 'add' ?>">
                            <?php if ($editCategory): ?>
                                <input type="hidden" name="id" value="<?= $editCategory['id'] ?>">
                            <?php endif; ?>

                            <div class="form-group">
                                <label>Nama Kategori <span class="required">*</span></label>
                                <input type="text" name="name" class="form-control" required
                                    value="<?= h($editCategory['name'] ?? '') ?>" placeholder="Contoh: Sosis">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Slug</label>
                                    <input type="text" name="slug" class="form-control"
                                        value="<?= h($editCategory['slug'] ?? '') ?>"
                                        placeholder="Auto-generate jika kosong">
                                </div>
                                <div class="form-group">
                                    <label>Icon (Emoji)</label>
                                    <input type="text" name="icon" class="form-control"
                                        value="<?= h($editCategory['icon'] ?? '') ?>" placeholder="🌭"
                                        style="font-size:1.5rem;text-align:center">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Deskripsi</label>
                                <input type="text" name="description" class="form-control"
                                    value="<?= h($editCategory['description'] ?? '') ?>"
                                    placeholder="Deskripsi singkat kategori">
                            </div>

                            <button type="submit" class="btn-admin btn-admin-primary">
                                <i class="bi bi-<?= $editCategory ? 'check-lg' : 'plus-lg' ?>"></i>
                                <?= $editCategory ? 'Simpan Perubahan' : 'Tambah Kategori' ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- CATEGORY LIST -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>
                            <?= count($categories) ?> Kategori
                        </h2>
                    </div>
                    <div class="admin-card-body" style="padding:0">
                        <?php if (empty($categories)): ?>
                            <div class="empty-state">
                                <i class="bi bi-tags"></i>
                                <p>Belum ada kategori.</p>
                            </div>
                        <?php else: ?>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Icon</th>
                                        <th>Nama</th>
                                        <th>Slug</th>
                                        <th>Produk</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $cat): ?>
                                        <tr>
                                            <td style="font-size:1.5rem;text-align:center">
                                                <?= h($cat['icon']) ?>
                                            </td>
                                            <td>
                                                <strong>
                                                    <?= h($cat['name']) ?>
                                                </strong>
                                                <?php if ($cat['description']): ?>
                                                    <br><small style="color:var(--admin-dark-400)">
                                                        <?= h($cat['description']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td><code><?= h($cat['slug']) ?></code></td>
                                            <td><span class="badge badge-primary">
                                                    <?= $cat['product_count'] ?> produk
                                                </span></td>
                                            <td>
                                                <div style="display:flex;gap:6px">
                                                    <a href="categories.php?action=edit&id=<?= $cat['id'] ?>"
                                                        class="btn-admin btn-admin-outline btn-icon" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <?php if ($cat['product_count'] == 0): ?>
                                                        <button class="btn-admin btn-admin-danger btn-icon"
                                                            onclick="confirmDeleteCat(<?= $cat['id'] ?>, '<?= h(addslashes($cat['name'])) ?>')"
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
            <h3>Hapus Kategori?</h3>
            <p id="deleteMsg">Apakah Anda yakin ingin menghapus kategori ini?</p>
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
        function confirmDeleteCat(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteMsg').textContent = `Apakah Anda yakin ingin menghapus kategori "${name}"?`;
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