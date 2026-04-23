<?php
session_start();
require_once __DIR__ . '/includes/config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE email = ? AND email_verified = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate reset token
                $resetToken = bin2hex(random_bytes(32));
                $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $stmt = $pdo->prepare("UPDATE users SET password_reset_token = ?, password_reset_expires = ? WHERE id = ?");
                $stmt->execute([$resetToken, $expiresAt, $user['id']]);

                // Send reset email
                $resetLink = SITE_URL . "/reset_password.php?token=" . $resetToken;
                $subject = SITE_NAME . " - Password Reset Request";
                $htmlBody = "
                    <h2>Password Reset Request</h2>
                    <p>Hi " . htmlspecialchars($user['full_name']) . ",</p>
                    <p>You requested a password reset for your " . SITE_NAME . " account.</p>
                    <p>Click the link below to reset your password:</p>
                    <p><a href='" . $resetLink . "' style='background:#2eb84e;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Reset Password</a></p>
                    <p><strong>Important:</strong> This link will expire in 1 hour.</p>
                    <p>If you didn't request this reset, please ignore this email.</p>
                    <p>If the button doesn't work, copy and paste this URL into your browser:<br>
                    <a href='" . $resetLink . "'>" . $resetLink . "</a></p>
                    <p>Best regards,<br>" . COMPANY_NAME . "</p>
                ";
                $plainBody = "Password Reset Request\n\nReset your password: " . $resetLink . "\n\nThis link expires in 1 hour.\n\nIf you didn't request this, ignore this email.";

                $mailResult = sendMail($email, $user['full_name'], $subject, $htmlBody, $plainBody);

                if ($mailResult['ok']) {
                    $message = 'Password reset link sent to your email address.';
                } else {
                    $error = 'Failed to send reset email. Please try again later.';
                }
            } else {
                $error = 'No verified account found with this email address.';
            }
        } catch (PDOException $e) {
            $error = 'An error occurred. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?= SITE_NAME ?></title>
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
                        <h2 class="mt-3 mb-1" style="color: var(--navy-dark); font-weight: 900;">Forgot Password</h2>
                        <p class="text-muted mb-0" style="font-size:.95rem;">Reset your password to regain access.</p>
                    </div>

                    <?php if ($message): ?>
                        <div class="admin-alert admin-alert-success">
                            <i class="bi bi-check-circle"></i>
                            <div>
                                <?= htmlspecialchars($message) ?>
                            </div>
                        </div>
                        <div class="admin-form-actions" style="border-top: none; padding-top: 0; margin-top: var(--space-lg);">
                            <a href="login.php" class="admin-btn admin-btn-primary w-100 justify-content-center" style="padding: var(--space-md) var(--space-lg);">
                                <i class="bi bi-arrow-left"></i> Back to Login
                            </a>
                        </div>
                    <?php elseif ($error): ?>
                        <div class="admin-alert admin-alert-danger">
                            <i class="bi bi-exclamation-circle"></i>
                            <div>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        </div>
                        <div class="admin-form-actions" style="border-top: none; padding-top: 0; margin-top: var(--space-lg);">
                            <a href="login.php" class="admin-btn admin-btn-secondary w-100 justify-content-center" style="padding: var(--space-md) var(--space-lg);">
                                <i class="bi bi-arrow-left"></i> Back to Login
                            </a>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-4" style="font-size:.95rem;">Enter your email address and we'll send you a link to reset your password.</p>

                        <form method="post" class="mt-3">
                            <div class="admin-form-group">
                                <label for="email" class="admin-form-label required">Email Address</label>
                                <input type="email" class="admin-form-input" id="email" name="email" autocomplete="email" required>
                            </div>
                            <div class="admin-form-actions" style="border-top: none; padding-top: 0; margin-top: var(--space-lg);">
                                <button type="submit" class="admin-btn admin-btn-primary w-100 justify-content-center" style="padding: var(--space-md) var(--space-lg);">
                                    <i class="bi bi-envelope"></i> Send Reset Link
                                </button>
                            </div>
                        </form>

                        <div class="d-flex justify-content-center align-items-center mt-3">
                            <a href="login.php" class="fs-sm text-muted">
                                <i class="bi bi-arrow-left"></i> Back to Login
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <p class="text-center mt-4 mb-0" style="color: rgba(255,255,255,0.7); font-size: .85rem;">
                    © <?= date('Y') ?> <?= SITE_NAME ?> · <?= SITE_TAGLINE ?>
                </p>
            </div>
        </div>
    </div>
</body>
</html>