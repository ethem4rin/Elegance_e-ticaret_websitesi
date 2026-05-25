<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/db.php';

$pdo = getDB();
$userId = $_SESSION['user_id'];

// Kullanıcının siparişlerini ürün detaylarıyla birlikte çekelim
$stmt = $pdo->prepare(
    'SELECT o.*, 
            (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
     FROM orders o 
     WHERE o.user_id = ? 
     ORDER BY o.created_at DESC'
);
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

$statusLabels = [
    'Pending'   => 'Beklemede',
    'Shipped'   => 'Kargoya Verildi',
    'Delivered' => 'Teslim Edildi',
];

include __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="page-title-section">
        <h1 class="page-title">Siparişlerim</h1>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"></path>
                <line x1="3" y1="6" x2="21" y2="6"></line>
            </svg>
            <h2>Henüz bir siparişiniz yok.</h2>
            <p>Şık koleksiyonumuza göz atarak ilk siparişinizi verebilirsiniz.</p>
            <a href="<?= SITE_URL ?>/products.php" class="btn btn--primary">Alışverişe Başla</a>
        </div>
    <?php else: ?>
        <div class="admin-card">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Sipariş No</th>
                            <th>Tarih</th>
                            <th>Ürün Sayısı</th>
                            <th>Toplam Tutar</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                            <td><?= $order['item_count'] ?> Ürün</td>
                            <td><strong>₺<?= number_format($order['total_price'], 2) ?></strong></td>
                            <td>
                                <span class="status-badge status-badge--<?= strtolower($order['status']) ?>">
                                    <?= $statusLabels[$order['status']] ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn--secondary btn--sm" onclick="alert('Sipariş detayları yakında eklenecek.')">Detay</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>