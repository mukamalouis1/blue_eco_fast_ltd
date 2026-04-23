<?php
session_start();
require_once __DIR__ . '/includes/config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $token = clean($_POST['token'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($newPassword) || strlen($newPassword) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email_verification_token = ? AND email_verification_expires > NOW()");
            $stmt->execute([$token]);
            $user = $stmt->fetch();

            if ($user) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ?, email_verified = 1, email_verification_token = NULL, email_verification_expires = NULL WHERE id = ?");
                $stmt->execute([$hashedPassword, $user['id']]);

                $message = 'Password updated successfully! You can now login with your new password.';
            } else {
                $error = 'Invalid or expired verification link.';
            }
        } catch (PDOException $e) {
            $error = 'An error occurred. Please try again.';
        }
    }
} elseif (isset($_GET['token'])) {
    $token = clean($_GET['token']);

    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE email_verification_token = ? AND email_verification_expires > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = 'Invalid or expired verification link.';
        }
    } catch (PDOException $e) {
        $error = 'An error occurred.';
    }
} else {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Email Verification & Password Setup</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-success">
                                <?= $message ?>
                                <br><br>
                                <a href="login.php" class="btn btn-primary">Go to Login</a>
                            </div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger">
                                <?= $error ?>
                                <br><br>
                                <a href="login.php" class="btn btn-secondary">Back to Login</a>
                            </div>
                        <?php elseif (isset($user)): ?>
                            <p>Welcome, <strong><?= htmlspecialchars($user['full_name']) ?></strong>!</p>
                            <p>Your email has been verified. Please set a new password for your account.</p>

                            <form method="post">
                                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password *</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                                    <div class="form-text">Must be at least 6 characters long.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password *</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                                </div>
                                <button type="submit" name="reset_password" class="btn btn-primary">Set Password & Login</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>