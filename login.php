<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/index.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';

$errors = [];
$success = $_GET['registered'] ?? false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Geçersiz istek. Lütfen tekrar deneyin.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $errors[] = 'Lütfen e-posta ve şifrenizi girin.';
        } else {
            $pdo = getDB();
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email']     = $user['email'];
                $_SESSION['role']      = $user['role'];
                // Regenerate session ID on login for security
                session_regenerate_id(true);
                header('Location: ' . SITE_URL . '/index.php');
                exit;
            } else {
                $errors[] = 'Geçersiz e-posta veya şifre.';
            }
        }
    }
}

$csrf = generateCsrfToken();
include __DIR__ . '/includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-card__header">
            <h1 class="auth-card__title">Tekrar Hoş Geldiniz</h1>
            <p class="auth-card__subtitle">Hesabınıza giriş yapın</p>
        </div>

        <?php if ($success): ?>
        <div class="alert alert--success">Kayıt başarılı! Lütfen giriş yapın.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
        <div class="alert alert--error">
            <?php foreach ($errors as $e): ?>
                <p><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="" class="auth-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

            <div class="form-group">
                <label for="email" class="form-label">E-posta Adresi</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="ornek@ornek.com" required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Şifre</label>
                <div class="input-password-wrap">
                    <input type="password" id="password" name="password" class="form-control"
                           placeholder="••••••••" required autocomplete="current-password">
                    <button type="button" class="password-toggle" data-target="password" aria-label="Şifreyi göster/gizle">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn--primary btn--full">Giriş Yap</button>
        </form>

        <p class="auth-card__footer">
            Hesabınız yok mu? <a href="<?= SITE_URL ?>/register.php">Hesap oluşturun</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
