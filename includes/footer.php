<footer class="footer">
    <div class="container">
        <div class="footer__grid">
            <div class="footer__brand">
                <h3 class="footer__logo">Élégance</h3>
                <p>Modern birey için zamansız stil.</p>
            </div>
            <div class="footer__links">
                <h4>Mağaza</h4>
                <ul>
                    <li><a href="<?= SITE_URL ?>/products.php">Tüm Ürünler</a></li>
                    <li><a href="<?= SITE_URL ?>/products.php?category_id=1">Erkek</a></li>
                    <li><a href="<?= SITE_URL ?>/products.php?category_id=2">Kadın</a></li>
                    <li><a href="<?= SITE_URL ?>/products.php?category_id=3">Aksesuarlar</a></li>
                    <li><a href="<?= SITE_URL ?>/products.php?category_id=4">Çanta</a></li>
                    <li><a href="<?= SITE_URL ?>/products.php?category_id=5">Çocuk</a></li>
                    <li><a href="<?= SITE_URL ?>/products.php?category_id=6">Ayakkabı</a></li>
                </ul>
            </div>
            <div class="footer__links">
                <h4>Hesap</h4>
                <ul>
                    <li><a href="<?= SITE_URL ?>/login.php">Giriş Yap</a></li>
                    <li><a href="<?= SITE_URL ?>/register.php">Kayıt Ol</a></li>
                    <li><a href="<?= SITE_URL ?>/cart.php">Sepet</a></li>
                </ul>
            </div>
        </div>
        <div class="footer__bottom">
            <p>&copy; <?= date('Y') ?> Élégance. Tüm hakları saklıdır.</p>
        </div>
    </div>
</footer>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</main>
</body>
</html>
