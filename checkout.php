<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/db.php';

$pdo = getDB();
$userId = $_SESSION['user_id'];
$user = getCurrentUser();

$errors = [];
$orderSuccess = false;
$orderId = null;

// Fetch cart items
$cartStmt = $pdo->prepare(
    'SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.stock_quantity
     FROM cart c
     JOIN products p ON c.product_id = p.id
     WHERE c.user_id = ?'
);
$cartStmt->execute([$userId]);
$cartItems = $cartStmt->fetchAll();

if (empty($cartItems)) {
    header('Location: ' . SITE_URL . '/cart.php');
    exit;
}

$subtotal = array_reduce($cartItems, fn($carry, $item) => $carry + $item['price'] * $item['quantity'], 0);
$shipping = $subtotal >= 150 ? 0 : 9.99;
$total = $subtotal + $shipping;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Geçersiz güvenlik anahtarı. Lütfen yenileyip tekrar deneyin.';
    } else {
        $shippingName    = trim($_POST['full_name'] ?? '');
        $shippingEmail   = trim($_POST['email'] ?? '');
        $shippingAddress = trim($_POST['address'] ?? '');
        $paymentMethod   = trim($_POST['payment_method'] ?? 'Kapıda Ödeme');

        if (empty($shippingName)) $errors[] = 'Ad soyad gereklidir.';
        if (empty($shippingEmail) || !filter_var($shippingEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Geçerli bir e-posta adresi gereklidir.';
        }
        if (empty($shippingAddress)) $errors[] = 'Teslimat adresi gereklidir.';

        // Validate stock
        foreach ($cartItems as $item) {
            if ($item['quantity'] > $item['stock_quantity']) {
                $errors[] = "\"" . htmlspecialchars($item['name']) . "\" için yalnızca {$item['stock_quantity']} adet stok var.";
            }
        }

        if (empty($errors)) {
            try {
                $pdo->beginTransaction();

                // Create order
                $orderStmt = $pdo->prepare(
                    'INSERT INTO orders
                    (
                        user_id,
                        total_price,
                        shipping_name,
                        shipping_email,
                        shipping_address,
                        payment_method
                    )
                    VALUES (?, ?, ?, ?, ?, ?)'
                );
                $orderStmt->execute([
                    $userId,
                    $total,
                    $shippingName,
                    $shippingEmail,
                    $shippingAddress,
                    $paymentMethod
                ]);
                $orderId = (int)$pdo->lastInsertId();

                // Insert order items and update stock
                $itemStmt  = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)');
                $stockStmt = $pdo->prepare('UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?');

                foreach ($cartItems as $item) {
                    $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
                    $stockStmt->execute([$item['quantity'], $item['product_id']]);
                }

                // Clear cart
                $pdo->prepare('DELETE FROM cart WHERE user_id = ?')->execute([$userId]);

                $pdo->commit();
                $orderSuccess = true;
            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = 'Sipariş oluşturulamadı. Lütfen tekrar deneyin.' . $e;
            }
        }
    }
}

$csrf = generateCsrfToken();
include __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="page-title-section">
        <h1 class="page-title">Ödeme</h1>
    </div>

    <?php if ($orderSuccess): ?>
    <div class="checkout-success">
        <div class="checkout-success__icon">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#4caf50" stroke-width="1.5">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>
        <h2>Siparişiniz başarıyla alındı!</h2>
        <p>Satın aldığınız için teşekkürler. Siparişiniz <strong>#<?= $orderId ?></strong> onaylandı.</p>
        <p class="checkout-success__sub">Onay e-postasını <?= htmlspecialchars($user['email'] ?? '') ?> adresine göndereceğiz</p>
        <div class="checkout-success__actions">
            <a href="<?= SITE_URL ?>/index.php" class="btn btn--primary">Alışverişe Devam Et</a>
        </div>
    </div>

    <?php else: ?>

    <?php if (!empty($errors)): ?>
    <div class="alert alert--error">
        <?php foreach ($errors as $e): ?>
            <p><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="checkout-layout">
        <!-- Checkout Form -->
        <div class="checkout-form-section">
            <h2 class="checkout-section-title">Teslimat Bilgileri</h2>
            <form method="POST" action="" class="checkout-form" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

                <div class="form-group">
                    <label for="full_name" class="form-label">Ad Soyad</label>
                    <input type="text" id="full_name" name="full_name" class="form-control"
                           value="<?= htmlspecialchars($_POST['full_name'] ?? $user['full_name'] ?? '') ?>"
                           required autocomplete="name">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">E-posta Adresi</label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($_POST['email'] ?? $user['email'] ?? '') ?>"
                           required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Teslimat Adresi</label>
                    <textarea id="address" name="address" class="form-control form-textarea" rows="4"
                              required autocomplete="street-address"><?= htmlspecialchars($_POST['address'] ?? $user['address'] ?? '') ?></textarea>
                </div>

                <!-- Ödeme Yöntemi Başlangıcı -->
                <div class="form-group">
                    <label for="payment_method" class="form-label">Ödeme Yöntemi</label>
                    <select id="payment_method" name="payment_method" class="form-control">
                        <option value="Kapıda Ödeme" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'Kapıda Ödeme') ? 'selected' : '' ?>>
                            Kapıda Ödeme
                        </option>
                        <option value="Kredi Kartı" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] === 'Kredi Kartı') ? 'selected' : '' ?>>
                            Kredi Kartı
                        </option>
                    </select>
                </div>

                <!-- Kredi Kartı Alanları Başlangıcı -->
                <div id="credit-card-fields" style="display:none; margin-top:20px;">
                    <div class="form-group">
                        <label class="form-label">Kart Üzerindeki İsim</label>
                        <input type="text" name="card_name" class="form-control"
                               value="<?= htmlspecialchars($_POST['card_name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kart Numarası</label>
                        <input type="text" name="card_number" class="form-control" maxlength="16" placeholder="1234123412341234"
                               value="<?= htmlspecialchars($_POST['card_number'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Son Kullanma Tarihi</label>
                        <input type="text" name="card_expiry" class="form-control" placeholder="12/28"
                               value="<?= htmlspecialchars($_POST['card_expiry'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">CVV</label>
                        <input type="text" name="card_cvv" class="form-control" maxlength="3" placeholder="123">
                    </div>
                </div>
                <!-- Kredi Kartı Alanları Bitişi -->

                <button type="submit" class="btn btn--primary btn--full">
                    Siparişi Ver — ₺<?= number_format($total, 2) ?>
                </button>
            </form>
        </div>

        <!-- Order Summary -->
        <aside class="checkout-summary">
            <h2 class="checkout-section-title">Sipariş Özeti</h2>
            <div class="checkout-items">
                <?php foreach ($cartItems as $item): ?>
                <div class="checkout-item">
                    <div class="checkout-item__name">
                        <?= htmlspecialchars($item['name']) ?>
                        <span class="checkout-item__qty">× <?= $item['quantity'] ?></span>
                    </div>
                    <div class="checkout-item__price">
                        ₺<?= number_format($item['price'] * $item['quantity'], 2) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="checkout-summary__rows">
                <div class="checkout-summary__row">
                    <span>Ara Toplam</span>
                    <span>₺<?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="checkout-summary__row">
                    <span>Kargo</span>
                    <span><?= $shipping === 0.0 ? 'Ücretsiz' : '₺' . number_format($shipping, 2) ?></span>
                </div>
                <div class="checkout-summary__divider"></div>
                <div class="checkout-summary__row checkout-summary__total">
                    <span>Toplam</span>
                    <span>₺<?= number_format($total, 2) ?></span>
                </div>
            </div>
        </aside>
    </div>
    <?php endif; ?>
</div>

<script>
    const paymentSelect = document.getElementById('payment_method');
    const creditCardFields = document.getElementById('credit-card-fields');

    function toggleCardFields() {
        if (paymentSelect.value === 'Kredi Kartı') {
            creditCardFields.style.display = 'block';
        } else {
            creditCardFields.style.display = 'none';
        }
    }

    paymentSelect.addEventListener('change', toggleCardFields);

    // Sayfa yüklendiğinde mevcut duruma göre göster/gizle
    toggleCardFields();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
