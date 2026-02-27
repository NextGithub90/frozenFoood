<?php
// ========================================
// ADMIN: PRODUCT MANAGEMENT (CRUD)
// ========================================

require_once __DIR__ . '/auth.php';
requireLogin();

// Get categories for form
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Determine action
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// ===== HANDLE POST ACTIONS =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    // --- ADD PRODUCT ---
    if ($postAction === 'add') {
        $name = trim($_POST['name'] ?? '');
        $category_id = (int) ($_POST['category_id'] ?? 0);
        $brand = trim($_POST['brand'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $weight = trim($_POST['weight'] ?? '');
        $storage = trim($_POST['storage'] ?? 'Disimpan beku (-18°C)');
        $price = trim($_POST['price'] ?? '');
        $halal = isset($_POST['halal']) ? 1 : 0;
        $badge = trim($_POST['badge'] ?? '');
        $is_new = isset($_POST['is_new']) ? 1 : 0;
        $is_best_seller = isset($_POST['is_best_seller']) ? 1 : 0;

        // Handle image upload
        $img = 'img/product-default.png';
        if (!empty($_FILES['img']['name']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
            $img = handleImageUpload($_FILES['img']);
            if (!$img) {
                setFlash('error', 'Gagal upload gambar. Pastikan format JPG/PNG/WEBP dan ukuran < 5MB.');
                redirect(BASE_URL . 'admin/products.php?action=add');
            }
        }

        $stmt = $pdo->prepare("INSERT INTO products (name, category_id, brand, description, weight, storage, price, img, halal, badge, is_new, is_best_seller) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category_id, $brand, $desc, $weight, $storage, $price, $img, $halal, $badge, $is_new, $is_best_seller]);

        setFlash('success', "Produk \"$name\" berhasil ditambahkan!");
        redirect(BASE_URL . 'admin/products.php');
    }

    // --- EDIT PRODUCT ---
    if ($postAction === 'edit') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $category_id = (int) ($_POST['category_id'] ?? 0);
        $brand = trim($_POST['brand'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $weight = trim($_POST['weight'] ?? '');
        $storage = trim($_POST['storage'] ?? 'Disimpan beku (-18°C)');
        $price = trim($_POST['price'] ?? '');
        $halal = isset($_POST['halal']) ? 1 : 0;
        $badge = trim($_POST['badge'] ?? '');
        $is_new = isset($_POST['is_new']) ? 1 : 0;
        $is_best_seller = isset($_POST['is_best_seller']) ? 1 : 0;

        // Handle image
        $img = $_POST['current_img'] ?? 'img/product-default.png';
        if (!empty($_FILES['img']['name']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
            $newImg = handleImageUpload($_FILES['img']);
            if ($newImg) {
                // Delete old uploaded image (not default ones)
                deleteProductImage($img);
                $img = $newImg;
            }
        }

        $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, brand=?, description=?, weight=?, storage=?, price=?, img=?, halal=?, badge=?, is_new=?, is_best_seller=? WHERE id=?");
        $stmt->execute([$name, $category_id, $brand, $desc, $weight, $storage, $price, $img, $halal, $badge, $is_new, $is_best_seller, $id]);

        setFlash('success', "Produk \"$name\" berhasil diperbarui!");
        redirect(BASE_URL . 'admin/products.php');
    }

    // --- DELETE PRODUCT ---
    if ($postAction === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);

        // Get product image path before delete
        $stmt = $pdo->prepare("SELECT img, name FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if ($product) {
            // Delete the image file from filesystem
            deleteProductImage($product['img']);

            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);

            setFlash('success', "Produk \"{$product['name']}\" berhasil dihapus beserta gambarnya!");
        }
        redirect(BASE_URL . 'admin/products.php');
    }
}

// ===== IMAGE HANDLING FUNCTIONS =====
function handleImageUpload($file)
{
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowed) || $file['size'] > $maxSize) {
        return false;
    }

    // Ensure upload directory exists
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'product_' . time() . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 8) . '.' . $ext;
    $destination = UPLOAD_DIR . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return UPLOAD_URL . $filename;
    }
    return false;
}

function deleteProductImage($imgPath)
{
    // Only delete uploaded images (in uploads/products/), not default images in img/
    if (empty($imgPath))
        return;
    if (strpos($imgPath, 'uploads/products/') === false)
        return;

    $fullPath = __DIR__ . '/../' . $imgPath;
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }
}

// ===== GET DATA FOR VIEWS =====
$editProduct = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $editProduct = $stmt->fetch();
    if (!$editProduct) {
        setFlash('error', 'Produk tidak ditemukan.');
        redirect(BASE_URL . 'admin/products.php');
    }
}

// Get all products for list
$searchQuery = trim($_GET['search'] ?? '');
$filterCategory = (int) ($_GET['category'] ?? 0);

$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($searchQuery) {
    $sql .= " AND (p.name LIKE ? OR p.brand LIKE ? OR p.description LIKE ?)";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
}
if ($filterCategory > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $filterCategory;
}
$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk — Admin Nada Agen Sosis</title>
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
            <a href="products.php" class="active"><i class="bi bi-box-seam-fill"></i> Produk</a>
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
                <h1><?= $action === 'add' ? 'Tambah Produk' : ($action === 'edit' ? 'Edit Produk' : 'Manajemen Produk') ?>
                </h1>
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

            <?php if ($action === 'add' || $action === 'edit'): ?>
                <!-- ===== ADD / EDIT FORM ===== -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2><i class="bi bi-<?= $action === 'add' ? 'plus-circle' : 'pencil-square' ?>"></i>
                            <?= $action === 'add' ? 'Tambah Produk Baru' : 'Edit Produk' ?>
                        </h2>
                        <a href="products.php" class="btn-admin btn-admin-outline btn-sm"><i class="bi bi-arrow-left"></i>
                            Kembali</a>
                    </div>
                    <div class="admin-card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="<?= $action ?>">
                            <?php if ($action === 'edit'): ?>
                                <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
                                <input type="hidden" name="current_img" value="<?= h($editProduct['img']) ?>">
                            <?php endif; ?>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Nama Produk <span class="required">*</span></label>
                                    <input type="text" name="name" class="form-control" required
                                        value="<?= h($editProduct['name'] ?? '') ?>"
                                        placeholder="Contoh: Sosis Kanzler Bratwurst 500g">
                                </div>
                                <div class="form-group">
                                    <label>Brand <span class="required">*</span></label>
                                    <input type="text" name="brand" class="form-control" required
                                        value="<?= h($editProduct['brand'] ?? '') ?>" placeholder="Contoh: Kanzler, Fiesta">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Kategori <span class="required">*</span></label>
                                    <select name="category_id" class="form-control" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= (($editProduct['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                                <?= h($cat['icon'] . ' ' . $cat['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Harga <span class="required">*</span></label>
                                    <input type="text" name="price" class="form-control" required
                                        value="<?= h($editProduct['price'] ?? '') ?>" placeholder="Contoh: Rp 65.000">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="description" class="form-control"
                                    placeholder="Deskripsi produk..."><?= h($editProduct['description'] ?? '') ?></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Berat / Isi</label>
                                    <input type="text" name="weight" class="form-control"
                                        value="<?= h($editProduct['weight'] ?? '') ?>"
                                        placeholder="Contoh: 500g (isi ±20 pcs)">
                                </div>
                                <div class="form-group">
                                    <label>Penyimpanan</label>
                                    <input type="text" name="storage" class="form-control"
                                        value="<?= h($editProduct['storage'] ?? 'Disimpan beku (-18°C)') ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Badge</label>
                                    <select name="badge" class="form-control">
                                        <option value="" <?= empty($editProduct['badge'] ?? '') ? 'selected' : '' ?>>Tidak
                                            ada</option>
                                        <option value="Laris" <?= ($editProduct['badge'] ?? '') === 'Laris' ? 'selected' : '' ?>>🔥 Laris</option>
                                        <option value="Baru" <?= ($editProduct['badge'] ?? '') === 'Baru' ? 'selected' : '' ?>>
                                            ✨ Baru</option>
                                        <option value="Promo" <?= ($editProduct['badge'] ?? '') === 'Promo' ? 'selected' : '' ?>>💰 Promo</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Gambar Produk</label>
                                    <input type="file" name="img" id="imgInput" class="form-control"
                                        accept="image/jpeg,image/png,image/webp" style="display:none">
                                    <div id="uploadZone"
                                        style="border:2px dashed var(--admin-dark-200);border-radius:12px;padding:20px;text-align:center;cursor:pointer;transition:all .3s;background:var(--admin-dark-100)">
                                        <i class="bi bi-cloud-arrow-up"
                                            style="font-size:2rem;color:var(--admin-primary)"></i>
                                        <p style="margin:8px 0 0;font-size:.88rem;color:var(--admin-dark-500)">Klik atau
                                            drag gambar ke sini<br><small>JPG, PNG, WEBP — Maks 5MB</small></p>
                                    </div>
                                    <!-- Progress bar popup -->
                                    <div id="uploadProgress" style="display:none;margin-top:12px">
                                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                                            <i class="bi bi-image" style="font-size:1.2rem;color:var(--admin-primary)"></i>
                                            <span id="uploadFileName"
                                                style="font-size:.85rem;color:var(--admin-dark-700);font-weight:600"></span>
                                        </div>
                                        <div
                                            style="background:var(--admin-dark-200);border-radius:8px;height:8px;overflow:hidden">
                                            <div id="progressBar"
                                                style="height:100%;width:0%;background:linear-gradient(90deg,var(--admin-primary),var(--admin-secondary));border-radius:8px;transition:width .3s ease">
                                            </div>
                                        </div>
                                        <small id="uploadStatus"
                                            style="display:block;margin-top:6px;font-size:.8rem;color:var(--admin-dark-400)">Memproses
                                            gambar...</small>
                                    </div>
                                    <!-- Image preview -->
                                    <div id="imgPreviewWrap"
                                        style="margin-top:12px;<?= ($action === 'edit' && !empty($editProduct['img'])) ? '' : 'display:none' ?>">
                                        <img id="imgPreview"
                                            src="<?= ($action === 'edit' && !empty($editProduct['img'])) ? '../' . h($editProduct['img']) : '' ?>"
                                            class="img-preview" alt="Preview"
                                            style="border:3px solid var(--admin-primary-50)">
                                        <div style="display:flex;align-items:center;gap:8px;margin-top:6px">
                                            <i class="bi bi-check-circle-fill" style="color:var(--admin-success)"></i>
                                            <small id="imgPreviewLabel"
                                                style="color:var(--admin-dark-400)"><?= ($action === 'edit') ? 'Gambar saat ini. Upload baru untuk mengganti.' : 'Gambar siap diupload.' ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="display:flex;gap:24px;margin-bottom:20px">
                                <div class="form-check">
                                    <input type="checkbox" name="halal" id="halal" <?= ($editProduct['halal'] ?? 1) ? 'checked' : '' ?>>
                                    <label for="halal">Halal</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="is_new" id="is_new" <?= ($editProduct['is_new'] ?? 0) ? 'checked' : '' ?>>
                                    <label for="is_new">Produk Baru</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="is_best_seller" id="is_best_seller"
                                        <?= ($editProduct['is_best_seller'] ?? 0) ? 'checked' : '' ?>>
                                    <label for="is_best_seller">Paling Laris</label>
                                </div>
                            </div>

                            <div style="display:flex;gap:12px">
                                <button type="submit" class="btn-admin btn-admin-primary">
                                    <i class="bi bi-<?= $action === 'add' ? 'plus-lg' : 'check-lg' ?>"></i>
                                    <?= $action === 'add' ? 'Tambah Produk' : 'Simpan Perubahan' ?>
                                </button>
                                <a href="products.php" class="btn-admin btn-admin-outline">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                <!-- ===== PRODUCT LIST ===== -->
                <div class="toolbar">
                    <div style="display:flex;gap:12px;align-items:center">
                        <div class="toolbar-search">
                            <i class="bi bi-search"></i>
                            <input type="text" id="searchInput" placeholder="Cari produk..." value="<?= h($searchQuery) ?>">
                        </div>
                        <div class="toolbar-filter">
                            <select id="categoryFilter">
                                <option value="0">Semua Kategori</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $filterCategory == $cat['id'] ? 'selected' : '' ?>>
                                        <?= h($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <a href="products.php?action=add" class="btn-admin btn-admin-primary"><i class="bi bi-plus-lg"></i>
                        Tambah Produk</a>
                </div>

                <div class="admin-card">
                    <div class="admin-card-header">
                        <h2>
                            <?= count($products) ?> Produk
                        </h2>
                    </div>
                    <div class="admin-card-body" style="padding:0;overflow-x:auto">
                        <?php if (empty($products)): ?>
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <p>Belum ada produk. <a href="products.php?action=add">Tambah produk baru</a></p>
                            </div>
                        <?php else: ?>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Gambar</th>
                                        <th>Nama Produk</th>
                                        <th>Kategori</th>
                                        <th>Brand</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $p): ?>
                                        <tr>
                                            <td><img src="../<?= h($p['img']) ?>" class="product-thumb" alt="<?= h($p['name']) ?>">
                                            </td>
                                            <td>
                                                <strong>
                                                    <?= h($p['name']) ?>
                                                </strong>
                                                <br><small style="color:var(--admin-dark-400)">
                                                    <?= h($p['weight']) ?>
                                                </small>
                                            </td>
                                            <td><span class="badge badge-primary">
                                                    <?= h($p['category_name']) ?>
                                                </span></td>
                                            <td>
                                                <?= h($p['brand']) ?>
                                            </td>
                                            <td><strong>
                                                    <?= h($p['price']) ?>
                                                </strong></td>
                                            <td>
                                                <?php if ($p['is_best_seller']): ?><span class="badge badge-warning">🔥 Laris</span>
                                                <?php endif; ?>
                                                <?php if ($p['is_new']): ?><span class="badge badge-info">✨ Baru</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div style="display:flex;gap:6px">
                                                    <a href="products.php?action=edit&id=<?= $p['id'] ?>"
                                                        class="btn-admin btn-admin-outline btn-icon" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button class="btn-admin btn-admin-danger btn-icon"
                                                        onclick="confirmDelete(<?= $p['id'] ?>, '<?= h(addslashes($p['name'])) ?>')"
                                                        title="Hapus">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Delete Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <i class="bi bi-exclamation-triangle-fill" style="font-size:3rem;color:var(--admin-danger)"></i>
            <h3>Hapus Produk?</h3>
            <p id="deleteMsg">Apakah Anda yakin ingin menghapus produk ini? Gambar produk juga akan dihapus dari server.
            </p>
            <div class="modal-actions">
                <button class="btn-admin btn-admin-outline" onclick="closeDeleteModal()">Batal</button>
                <form method="POST" id="deleteForm" style="display:inline">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="deleteId">
                    <button type="submit" class="btn-admin btn-admin-danger"><i class="bi bi-trash3"></i> Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Search & filter
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');

        function applyFilters() {
            const search = searchInput ? searchInput.value : '';
            const category = categoryFilter ? categoryFilter.value : '0';
            window.location.href = `products.php?search=${encodeURIComponent(search)}&category=${category}`;
        }

        if (searchInput) {
            let timeout;
            searchInput.addEventListener('input', () => {
                clearTimeout(timeout);
                timeout = setTimeout(applyFilters, 500);
            });
        }
        if (categoryFilter) {
            categoryFilter.addEventListener('change', applyFilters);
        }

        // Delete modal
        function confirmDelete(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteMsg').textContent = `Apakah Anda yakin ingin menghapus produk "${name}"? Gambar produk juga akan dihapus dari server.`;
            document.getElementById('deleteModal').classList.add('active');
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
        }
        document.getElementById('deleteModal').addEventListener('click', function (e) {
            if (e.target === this) closeDeleteModal();
        });

        // ====== IMAGE UPLOAD WITH PROGRESS BAR ======
        const imgInput = document.getElementById('imgInput');
        const uploadZone = document.getElementById('uploadZone');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('progressBar');
        const uploadFileName = document.getElementById('uploadFileName');
        const uploadStatus = document.getElementById('uploadStatus');
        const imgPreviewWrap = document.getElementById('imgPreviewWrap');
        const imgPreview = document.getElementById('imgPreview');
        const imgPreviewLabel = document.getElementById('imgPreviewLabel');

        if (uploadZone && imgInput) {
            // Click to open file dialog
            uploadZone.addEventListener('click', () => imgInput.click());

            // Drag & drop
            uploadZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadZone.style.borderColor = 'var(--admin-primary)';
                uploadZone.style.background = 'var(--admin-primary-50)';
            });
            uploadZone.addEventListener('dragleave', () => {
                uploadZone.style.borderColor = 'var(--admin-dark-200)';
                uploadZone.style.background = 'var(--admin-dark-100)';
            });
            uploadZone.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadZone.style.borderColor = 'var(--admin-dark-200)';
                uploadZone.style.background = 'var(--admin-dark-100)';
                if (e.dataTransfer.files.length > 0) {
                    imgInput.files = e.dataTransfer.files;
                    handleFileSelect(e.dataTransfer.files[0]);
                }
            });

            // File input change
            imgInput.addEventListener('change', () => {
                if (imgInput.files.length > 0) {
                    handleFileSelect(imgInput.files[0]);
                }
            });
        }

        function handleFileSelect(file) {
            // Validate file
            const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Format tidak didukung! Gunakan JPG, PNG, atau WEBP.');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar! Maksimal 5MB.');
                return;
            }

            // Show progress
            uploadFileName.textContent = file.name;
            uploadProgress.style.display = 'block';
            progressBar.style.width = '0%';
            uploadStatus.textContent = 'Memproses gambar...';
            imgPreviewWrap.style.display = 'none';

            // Simulate progress animation
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 25 + 10;
                if (progress >= 90) {
                    progress = 90;
                    clearInterval(interval);
                }
                progressBar.style.width = progress + '%';
            }, 100);

            // Read the file
            const reader = new FileReader();
            reader.onload = (e) => {
                // Complete progress
                clearInterval(interval);
                progressBar.style.width = '100%';
                uploadStatus.textContent = '✅ Gambar berhasil dimuat!';
                uploadStatus.style.color = 'var(--admin-success)';

                // Show preview after a short delay
                setTimeout(() => {
                    imgPreview.src = e.target.result;
                    imgPreviewWrap.style.display = 'block';
                    imgPreviewLabel.textContent = 'Gambar baru siap diupload. Klik Simpan Perubahan untuk menerapkan.';

                    // Update upload zone text
                    uploadZone.innerHTML = `
                        <i class="bi bi-check-circle-fill" style="font-size:2rem;color:var(--admin-success)"></i>
                        <p style="margin:8px 0 0;font-size:.88rem;color:var(--admin-success);font-weight:600">${file.name}<br><small style="color:var(--admin-dark-400)">Klik untuk ganti gambar lain</small></p>
                    `;

                    // Hide progress after showing preview
                    setTimeout(() => {
                        uploadProgress.style.display = 'none';
                    }, 500);
                }, 400);
            };
            reader.readAsDataURL(file);
        }
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