<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
requireAdmin();

$id = $_SESSION['id'];
$message = '';
$error = '';

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $admin = $stmt->fetch();

    if (!$admin) {
        session_destroy();
        header('Location: ../login.php');
        exit;
    }
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = clean($_POST['full_name'] ?? '');
    $phone = clean($_POST['phone'] ?? '');

    $errors = [];
    if ($full_name === '') {
        $errors[] = 'Full name is required.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
            $stmt->execute([$full_name, $phone, $id]);
            $message = 'Profile updated successfully!';

            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $admin = $stmt->fetch();
        } catch (PDOException $e) {
            $error = 'Update failed. Please try again.';
        }
    } else {
        $error = implode(' ', $errors);
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];
    if ($current_password === '') $errors[] = 'Current password is required.';
    if ($new_password === '' || strlen($new_password) < 6) $errors[] = 'New password must be at least 6 characters.';
    if ($new_password !== $confirm_password) $errors[] = 'New passwords do not match.';
    if (!password_verify($current_password, $admin['password'])) $errors[] = 'Current password is incorrect.';

    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $id]);
            $message = 'Password changed successfully!';
        } catch (PDOException $e) {
            $error = 'Password change failed. Please try again.';
        }
    } else {
        $error = implode(' ', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h5><i class="bi bi-person-circle"></i> Profile</h5>
                <p>Account settings</p>
            </div>
            <nav>
                <ul class="admin-nav">
                    <li class="admin-nav-item"><a href="dashboard.php" class="admin-nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li class="admin-nav-item"><a href="enquiries.php" class="admin-nav-link"><i class="bi bi-envelope"></i> Enquiries</a></li>
                    <li class="admin-nav-item"><a href="cars.php" class="admin-nav-link"><i class="bi bi-ev-front"></i> Cars</a></li>
                    <li class="admin-nav-item"><a href="users.php" class="admin-nav-link"><i class="bi bi-people"></i> Users</a></li>
                    <li class="admin-nav-item"><a href="settings.php" class="admin-nav-link"><i class="bi bi-gear"></i> Settings</a></li>
                </ul>
            </nav>
            <hr class="admin-nav-divider">
            <ul class="admin-nav">
                <li class="admin-nav-item"><a href="profile.php" class="admin-nav-link active"><i class="bi bi-person-circle"></i> My Profile</a></li>
                <li class="admin-nav-item"><a href="../logout.php" class="admin-nav-link"><i class="bi bi-box-arrow-left"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="admin-main-content">
            <div class="admin-page-header">
                <h1 class="admin-page-title"><i class="bi bi-person-circle"></i> My Profile</h1>
                <p class="admin-page-subtitle">Update your details and secure your account.</p>
            </div>

            <?php if ($message): ?>
                <div class="admin-alert admin-alert-success">
                    <i class="bi bi-check-circle"></i>
                    <div><?= htmlspecialchars($message) ?></div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="admin-alert admin-alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <div class="admin-form-card">
                <div class="admin-form-header">
                    <h2><i class="bi bi-person-lines-fill"></i> Profile Information</h2>
                </div>
                <form method="post" class="admin-form">
                    <div class="admin-form-row">
                        <div class="admin-form-col">
                            <label class="admin-form-label">Email Address</label>
                            <input type="email" class="admin-form-input" value="<?= htmlspecialchars($admin['email']) ?>" readonly>
                            <small class="admin-form-hint">Email cannot be changed</small>
                        </div>
                        <div class="admin-form-col">
                            <label class="admin-form-label required">Full Name</label>
                            <input type="text" class="admin-form-input" name="full_name" value="<?= htmlspecialchars($admin['full_name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="admin-form-row">
                        <div class="admin-form-col">
                            <label class="admin-form-label">Phone Number</label>
                            <input type="tel" class="admin-form-input" name="phone" value="<?= htmlspecialchars($admin['phone'] ?? '') ?>">
                        </div>
                        <div class="admin-form-col">
                            <label class="admin-form-label">Member Since</label>
                            <input type="text" class="admin-form-input" value="<?= htmlspecialchars(date('d M Y', strtotime($admin['created_at'] ?? 'now'))) ?>" readonly>
                        </div>
                    </div>

                    <div class="admin-form-actions">
                        <button type="submit" name="update_profile" class="admin-btn admin-btn-primary">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <div class="admin-form-card">
                <div class="admin-form-header">
                    <h2><i class="bi bi-shield-lock"></i> Change Password</h2>
                </div>
                <form method="post" class="admin-form">
                    <div class="admin-form-row">
                        <div class="admin-form-col">
                            <label class="admin-form-label required">Current Password</label>
                            <input type="password" class="admin-form-input" name="current_password" required>
                        </div>
                    </div>
                    <div class="admin-form-row">
                        <div class="admin-form-col">
                            <label class="admin-form-label required">New Password</label>
                            <input type="password" class="admin-form-input" name="new_password" minlength="6" required>
                            <small class="admin-form-hint">Minimum 6 characters</small>
                        </div>
                        <div class="admin-form-col">
                            <label class="admin-form-label required">Confirm New Password</label>
                            <input type="password" class="admin-form-input" name="confirm_password" minlength="6" required>
                        </div>
                    </div>

                    <div class="admin-form-actions">
                        <button type="submit" name="change_password" class="admin-btn admin-btn-secondary">
                            <i class="bi bi-key"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <button class="admin-sidebar-toggle" type="button" id="adminSidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const toggle = document.getElementById('adminSidebarToggle');
            const sidebar = document.querySelector('.admin-sidebar');
            if (!toggle || !sidebar) return;
            toggle.addEventListener('click', () => sidebar.classList.toggle('active'));
        })();
    </script>
</body>
</html>

