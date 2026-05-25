<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$pdo = getDB();

// Fetch 6 latest products
$featuredStmt = $pdo->query('SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 6');
$featuredProducts = $featuredStmt->fetchAll();

// Fetch categories
$catStmt = $pdo->query('SELECT * FROM categories ORDER BY id');
$categories = $catStmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero__overlay"></div>
    <div class="hero__content">
        <p class="hero__eyebrow">Yeni Koleksiyon 2024</p>
        <h1 class="hero__title">Zamansız<br>Zarafet</h1>
        <p class="hero__subtitle">Modern birey için özenle hazırlanmış lüks modayı keşfedin.</p>
        <a href="<?= SITE_URL ?>/products.php" class="btn btn--hero">Koleksiyonu İncele</a>
    </div>
</section>

<!-- Featured Products -->
<section class="section">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title">Öne Çıkan Parçalar</h2>
            <p class="section__subtitle">Seçkin gardırop için özenle seçildi</p>
        </div>
        <div class="products-grid">
            <?php foreach ($featuredProducts as $product): ?>
            <article class="product-card" data-product-id="<?= $product['id'] ?>">
                <a href="<?= SITE_URL ?>/product.php?id=<?= $product['id'] ?>" class="product-card__link">
                    <div class="product-card__image">
                        <?php if (!empty($product['image_url']) && str_starts_with($product['image_url'], 'http')): ?>
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                        <svg viewBox="0 0 300 400" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <rect width="300" height="400" fill="#f0ede8"/>
                            <text x="150" y="210" text-anchor="middle" fill="#c0b8b0" font-family="Georgia, serif" font-size="72"><?= htmlspecialchars(mb_substr($product['name'], 0, 1)) ?></text>
                        </svg>
                        <?php endif; ?>
                    </div>
                    <div class="product-card__body">
                        <p class="product-card__category"><?= htmlspecialchars($product['category_name'] ?? '') ?></p>
                        <h3 class="product-card__title"><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="product-card__price">₺<?= number_format($product['price'], 2) ?></p>
                    </div>
                </a>
                <div class="product-card__footer">
                    <button class="btn btn--primary btn--full add-to-cart-btn"
                            data-product-id="<?= $product['id'] ?>"
                            <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                        <?= $product['stock_quantity'] > 0 ? 'Sepete Ekle' : 'Stokta Yok' ?>
                    </button>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <div class="section__cta">
            <a href="<?= SITE_URL ?>/products.php" class="btn btn--secondary">Tüm Ürünleri Gör</a>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section section--gray">
    <div class="container">
        <div class="section__header">
            <h2 class="section__title">Kategorilere Göre Alışveriş</h2>
            <p class="section__subtitle">Özenle hazırlanmış koleksiyonlarımızı keşfedin</p>
        </div>
        <div class="categories-grid">
            <div class="category-card">
                <a href="<?= SITE_URL ?>/products.php?category_id=1" class="category-card__link">
                    <div class="category-card__image">
                        <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                            <rect width="400" height="300" fill="#1a1a1a"/>
                            <text x="200" y="140" text-anchor="middle" fill="#f0ede8" font-family="Georgia, serif" font-size="24" letter-spacing="4">ERKEK</text>
                            <line x1="160" y1="160" x2="240" y2="160" stroke="#8c7b6b" stroke-width="1"/>
                            <text x="200" y="185" text-anchor="middle" fill="#8c7b6b" font-family="Georgia, serif" font-size="13" letter-spacing="2">KOLEKSİYON</text>
                        </svg>
                    </div>
                    <div class="category-card__body">
                        <h3>Erkek</h3>
                        <span class="category-card__explore">Keşfet →</span>
                    </div>
                </a>
            </div>
            <div class="category-card">
                <a href="<?= SITE_URL ?>/products.php?category_id=2" class="category-card__link">
                    <div class="category-card__image">
                        <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                            <rect width="400" height="300" fill="#8c7b6b"/>
                            <text x="200" y="140" text-anchor="middle" fill="#fff" font-family="Georgia, serif" font-size="24" letter-spacing="4">KADIN</text>
                            <line x1="155" y1="160" x2="245" y2="160" stroke="rgba(255,255,255,0.5)" stroke-width="1"/>
                            <text x="200" y="185" text-anchor="middle" fill="rgba(255,255,255,0.7)" font-family="Georgia, serif" font-size="13" letter-spacing="2">KOLEKSİYON</text>
                        </svg>
                    </div>
                    <div class="category-card__body">
                        <h3>Kadın</h3>
                        <span class="category-card__explore">Keşfet →</span>
                    </div>
                </a>
            </div>
            <div class="category-card">
                <a href="<?= SITE_URL ?>/products.php?category_id=3" class="category-card__link">
                    <div class="category-card__image">
                        <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                            <rect width="400" height="300" fill="#e8e0d8"/>
                            <text x="200" y="130" text-anchor="middle" fill="#1a1a1a" font-family="Georgia, serif" font-size="20" letter-spacing="3">AKSESUAR</text>
                            <line x1="140" y1="150" x2="260" y2="150" stroke="#8c7b6b" stroke-width="1"/>
                            <text x="200" y="175" text-anchor="middle" fill="#8c7b6b" font-family="Georgia, serif" font-size="13" letter-spacing="2">KOLEKSİYON</text>
                        </svg>
                    </div>
                    <div class="category-card__body">
                        <h3>Aksesuarlar</h3>
                        <span class="category-card__explore">Keşfet →</span>
                    </div>
                </a>
            </div>
            <div class="category-card">
                <a href="<?= SITE_URL ?>/products.php?category_id=4" class="category-card__link">
                    <div class="category-card__image">
                        <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                            <rect width="400" height="300" fill="#d5c4b2"/>
                            <text x="200" y="140" text-anchor="middle" fill="#3a2f24" font-family="Georgia, serif" font-size="22" letter-spacing="3">ÇANTA</text>
                            <line x1="150" y1="160" x2="250" y2="160" stroke="#8c7b6b" stroke-width="1"/>
                            <text x="200" y="185" text-anchor="middle" fill="#8c7b6b" font-family="Georgia, serif" font-size="12" letter-spacing="2">KOLEKSİYON</text>
                        </svg>
                    </div>
                    <div class="category-card__body">
                        <h3>Çanta</h3>
                        <span class="category-card__explore">Keşfet →</span>
                    </div>
                </a>
            </div>
            <div class="category-card">
                <a href="<?= SITE_URL ?>/products.php?category_id=5" class="category-card__link">
                    <div class="category-card__image">
                        <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                            <rect width="400" height="300" fill="#b7c3d2"/>
                            <text x="200" y="140" text-anchor="middle" fill="#1f2b3a" font-family="Georgia, serif" font-size="22" letter-spacing="3">ÇOCUK</text>
                            <line x1="150" y1="160" x2="250" y2="160" stroke="#5b6a7b" stroke-width="1"/>
                            <text x="200" y="185" text-anchor="middle" fill="#5b6a7b" font-family="Georgia, serif" font-size="12" letter-spacing="2">KOLEKSİYON</text>
                        </svg>
                    </div>
                    <div class="category-card__body">
                        <h3>Çocuk</h3>
                        <span class="category-card__explore">Keşfet →</span>
                    </div>
                </a>
            </div>
            <div class="category-card">
                <a href="<?= SITE_URL ?>/products.php?category_id=6" class="category-card__link">
                    <div class="category-card__image">
                        <svg viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                            <rect width="400" height="300" fill="#f1e6d0"/>
                            <text x="200" y="140" text-anchor="middle" fill="#463f34" font-family="Georgia, serif" font-size="20" letter-spacing="3">AYAKKABI</text>
                            <line x1="140" y1="160" x2="260" y2="160" stroke="#8c7b6b" stroke-width="1"/>
                            <text x="200" y="185" text-anchor="middle" fill="#8c7b6b" font-family="Georgia, serif" font-size="12" letter-spacing="2">KOLEKSİYON</text>
                        </svg>
                    </div>
                    <div class="category-card__body">
                        <h3>Ayakkabı</h3>
                        <span class="category-card__explore">Keşfet →</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Value Proposition -->
<section class="section">
    <div class="container">
        <div class="values-grid">
            <div class="value-item">
                <div class="value-item__icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </div>
                <h4>Ücretsiz Kargo</h4>
                <p>₺150 üzeri tüm siparişlerde</p>
            </div>
            <div class="value-item">
                <div class="value-item__icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                    </svg>
                </div>
                <h4>Kolay İade</h4>
                <p>30 gün koşulsuz iade</p>
            </div>
            <div class="value-item">
                <div class="value-item__icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h4>Güvenli Ödeme</h4>
                <p>SSL şifreli ödeme</p>
            </div>
            <div class="value-item">
                <div class="value-item__icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 8v4l3 3"/>
                    </svg>
                </div>
                <h4>7/24 Destek</h4>
                <p>Her zaman yanınızdayız</p>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
