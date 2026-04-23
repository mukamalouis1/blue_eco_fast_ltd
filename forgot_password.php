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
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Forgot Password</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-success">
                                <?= $message ?>
                                <br><br>
                                <a href="login.php" class="btn btn-primary">Back to Login</a>
                            </div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger">
                                <?= $error ?>
                                <br><br>
                                <a href="login.php" class="btn btn-secondary">Back to Login</a>
                            </div>
                        <?php else: ?>
                            <p>Enter your email address and we'll send you a link to reset your password.</p>

                            <form method="post">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Send Reset Link</button>
                                <a href="login.php" class="btn btn-secondary ms-2">Back to Login</a>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>