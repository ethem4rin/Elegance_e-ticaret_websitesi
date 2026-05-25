<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
requireAdmin();
require_once dirname(__DIR__) . '/includes/db.php';

$pdo = getDB();
$errors = [];
$editProduct = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Geçersiz güvenlik anahtarı.';
    } else {
        $action = $_POST['form_action'] ?? '';

        if ($action === 'add' || $action === 'edit') {
            $name        = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price       = (float)($_POST['price'] ?? 0);
            $image_url   = trim($_POST['image_url'] ?? '');
            $stock       = max(0, (int)($_POST['stock_quantity'] ?? 0));
            $categoryId  = (int)($_POST['category_id'] ?? 0) ?: null;

            if (empty($name))  $errors[] = 'Ürün adı gereklidir.';
            if ($price <= 0)   $errors[] = 'Fiyat sıfırdan büyük olmalıdır.';

            if (empty($errors)) {
                if ($action === 'add') {
                    $stmt = $pdo->prepare('INSERT INTO products (name, description, price, image_url, stock_quantity, category_id) VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$name, $description, $price, $image_url, $stock, $categoryId]);
                } else {
                    $id = (int)($_POST['product_id'] ?? 0);
                    $stmt = $pdo->prepare('UPDATE products SET name=?, description=?, price=?, image_url=?, stock_quantity=?, category_id=? WHERE id=?');
                    $stmt->execute([$name, $description, $price, $image_url, $stock, $categoryId, $id]);
                }
                header('Location: ' . SITE_URL . '/admin/products.php?saved=1');
                exit;
            }

        } elseif ($action === 'delete') {
            $id = (int)($_POST['product_id'] ?? 0);
            $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
            header('Location: ' . SITE_URL . '/admin/products.php?deleted=1');
            exit;
        }
    }
}

// Edit mode
if ($_GET['action'] ?? '' === 'edit' && !empty($_GET['id'])) {
    $editStmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $editStmt->execute([(int)$_GET['id']]);
    $editProduct = $editStmt->fetch();
}

// Fetch all products
$products = $pdo->query('SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC')->fetchAll();
$categories = $pdo->query('SELECT * FROM categories ORDER BY category_name')->fetchAll();
$csrf = generateCsrfToken();

include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-layout">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="admin-main">
        <div class="admin-topbar">
            <h1 class="admin-page-title">Ürünler</h1>
        </div>

        <?php if (isset($_GET['saved'])): ?>
            <div class="alert alert--success">Ürün başarıyla kaydedildi.</div>
        <?php elseif (isset($_GET['deleted'])): ?>
            <div class="alert alert--success">Ürün silindi.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert--error"><?php foreach ($errors as $e) echo '<p>' . htmlspecialchars($e) . '</p>'; ?></div>
        <?php endif; ?>

        <!-- Add / Edit Form -->
        <div class="admin-card">
            <div class="admin-card__header">
                <h2 class="admin-card__title"><?= $editProduct ? 'Ürünü Düzenle' : 'Yeni Ürün Ekle' ?></h2>
                <?php if ($editProduct): ?>
                    <a href="<?= SITE_URL ?>/admin/products.php" class="btn btn--secondary btn--sm">Düzenlemeyi İptal Et</a>
                <?php endif; ?>
            </div>
            <form method="POST" action="" class="admin-form">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="form_action" value="<?= $editProduct ? 'edit' : 'add' ?>">
                <?php if ($editProduct): ?>
                    <input type="hidden" name="product_id" value="<?= $editProduct['id'] ?>">
                <?php endif; ?>

                <div class="admin-form__grid">
                    <div class="form-group">
                        <label class="form-label">Ürün Adı *</label>
                        <input type="text" name="name" class="form-control"
                               value="<?= htmlspecialchars($editProduct['name'] ?? $_POST['name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-control">
                            <option value="">— Yok —</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"
                                    <?= ($editProduct['category_id'] ?? null) == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Fiyat (₺) *</label>
                        <input type="number" name="price" class="form-control" step="0.01" min="0"
                               value="<?= htmlspecialchars($editProduct['price'] ?? $_POST['price'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stok Adedi</label>
                        <input type="number" name="stock_quantity" class="form-control" min="0"
                               value="<?= htmlspecialchars($editProduct['stock_quantity'] ?? $_POST['stock_quantity'] ?? '0') ?>">
                    </div>
                    <div class="form-group admin-form__full">
                        <label class="form-label">Görsel URL</label>
                        <input type="url" name="image_url" class="form-control" placeholder="https://..."
                               value="<?= htmlspecialchars($editProduct['image_url'] ?? $_POST['image_url'] ?? '') ?>">
                    </div>
                    <div class="form-group admin-form__full">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control form-textarea" rows="4"><?= htmlspecialchars($editProduct['description'] ?? $_POST['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn--primary">
                    <?= $editProduct ? 'Ürünü Güncelle' : 'Ürün Ekle' ?>
                </button>
            </form>
        </div>

        <!-- Products Table -->
        <div class="admin-card">
            <div class="admin-card__header">
                <h2 class="admin-card__title">Tüm Ürünler (<?= count($products) ?>)</h2>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ad</th>
                            <th>Kategori</th>
                            <th>Fiyat</th>
                            <th>Stok</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                        <tr><td colspan="6" class="admin-table__empty">Henüz ürün yok.</td></tr>
                        <?php else: ?>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
                            <td>₺<?= number_format($p['price'], 2) ?></td>
                            <td><?= $p['stock_quantity'] ?></td>
                            <td class="admin-table__actions">
                                <a href="<?= SITE_URL ?>/admin/products.php?action=edit&id=<?= $p['id'] ?>"
                                   class="btn btn--secondary btn--sm">Düzenle</a>
                                <form method="POST" action="" style="display:inline"
                                      onsubmit="return confirm('Bu ürün silinsin mi?')">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="hidden" name="form_action" value="delete">
                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="btn btn--danger btn--sm">Sil</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
