<?php
session_start();
require_once __DIR__ . '/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = [];

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (empty($password)) {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("SELECT id, email, password, role, email_verified FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                if ($user['email_verified'] == 0) {
                    $errors[] = 'Please verify your email address before logging in. Check your email for the verification link.';
                } else {
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];
                    // App is admin-only.
                    $_SESSION['is_admin'] = true;
                    header('Location: admin/dashboard.php');
                    exit;
                }
            } else {
                $errors[] = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Login failed. Please try again.'.$e;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="min-height:100vh; background: var(--gradient-secondary);">
    <div class="container" style="min-height:100vh; display:flex; align-items:center; padding: 80px 0;">
        <div class="row justify-content-center w-100">
            <div class="col-md-8 col-lg-5">
                <div class="form-card" style="padding: var(--space-2xl);">
                    <div class="text-center mb-4">
                        <a href="index.php" class="d-inline-flex align-items-center gap-2" style="text-decoration:none;">
                            <img src="images/logo.png" alt="<?= SITE_NAME ?>" style="height:42px;width:auto;">
                        </a>
                        <h2 class="mt-3 mb-1" style="color: var(--navy-dark); font-weight: 900;">Admin Login</h2>
                        <p class="text-muted mb-0" style="font-size:.95rem;">Sign in to manage enquiries, cars and users.</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="admin-alert admin-alert-danger">
                            <i class="bi bi-exclamation-circle"></i>
                            <div>
                                <?php foreach ($errors as $error): ?>
                                    <div><?= htmlspecialchars($error) ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="mt-3">
                        <div class="admin-form-group">
                            <label for="email" class="admin-form-label required">Email</label>
                            <input type="email" class="admin-form-input" id="email" name="email" autocomplete="email" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="password" class="admin-form-label required">Password</label>
                            <input type="password" class="admin-form-input" id="password" name="password" autocomplete="current-password" required>
                        </div>
                        <div class="admin-form-actions" style="border-top: none; padding-top: 0; margin-top: 0;">
                            <button type="submit" class="admin-btn admin-btn-primary w-100 justify-content-center" style="padding: var(--space-md) var(--space-lg);">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </div>
                    </form>

                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <a href="forgot_password.php" class="fs-sm">Forgot password?</a>
                        <a href="index.php" class="fs-sm text-muted">Back to website</a>
                    </div>
                </div>

                <p class="text-center mt-4 mb-0" style="color: rgba(255,255,255,0.7); font-size: .85rem;">
                    © <?= date('Y') ?> <?= SITE_NAME ?> · <?= SITE_TAGLINE ?>
                </p>
            </div>
        </div>
    </div>
</body>
</html>