<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
requireAdmin();
require_once dirname(__DIR__) . '/includes/db.php';

$pdo = getDB();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Geçersiz güvenlik anahtarı.';
    } else {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status  = $_POST['status'] ?? '';
        $allowed = ['Pending', 'Shipped', 'Delivered'];

        if ($orderId > 0 && in_array($status, $allowed)) {
            $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$status, $orderId]);
            header('Location: ' . SITE_URL . '/admin/orders.php?updated=1');
            exit;
        } else {
            $errors[] = 'Geçersiz sipariş veya durum.';
        }
    }
}

// Pagination
$perPage = 20;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$total = (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));

$orders = $pdo->prepare(
    'SELECT o.*, u.full_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT ? OFFSET ?'
);
$orders->execute([$perPage, $offset]);
$orders = $orders->fetchAll();

$csrf = generateCsrfToken();
$statusLabels = [
    'Pending' => 'Beklemede',
    'Shipped' => 'Kargoya Verildi',
    'Delivered' => 'Teslim Edildi',
];
include dirname(__DIR__) . '/includes/header.php';
?>

<div class="admin-layout">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="admin-main">
        <div class="admin-topbar">
            <h1 class="admin-page-title">Siparişler</h1>
        </div>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert--success">Sipariş durumu güncellendi.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert--error"><?php foreach ($errors as $e) echo '<p>' . htmlspecialchars($e) . '</p>'; ?></div>
        <?php endif; ?>

        <div class="admin-card">
            <div class="admin-card__header">
                <h2 class="admin-card__title">Tüm Siparişler (<?= $total ?>)</h2>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Müşteri</th>
                            <th>Toplam</th>
                            <th>Durum</th>
                            <th>Tarih</th>
                            <th>Durumu Güncelle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                        <tr><td colspan="6" class="admin-table__empty">Henüz sipariş yok.</td></tr>
                        <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td><?= htmlspecialchars($order['full_name'] ?? 'Bilinmiyor') ?></td>
                            <td>₺<?= number_format($order['total_price'], 2) ?></td>
                            <td>
                                <span class="status-badge status-badge--<?= strtolower($order['status']) ?>">
                                    <?= htmlspecialchars($statusLabels[$order['status']] ?? $order['status']) ?>
                                </span>
                            </td>
                            <td><?= date('d.m.Y', strtotime($order['created_at'])) ?></td>
                            <td>
                                <form method="POST" action="" class="admin-status-form">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <div class="admin-status-form__inner">
                                        <select name="status" class="form-control form-control--sm">
                                            <?php foreach (['Pending','Shipped','Delivered'] as $s): ?>
                                            <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= $statusLabels[$s] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn--primary btn--sm">Güncelle</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="admin-pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="pagination__btn <?= $i === $page ? 'pagination__btn--active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
