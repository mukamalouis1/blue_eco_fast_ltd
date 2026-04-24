<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
requireAdmin();

$id = $_SESSION['id'];
$pdo = getDB();
$message = $error = '';

// Get current admin
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$admin = $stmt->fetch();

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!password_verify($current_password, $admin['password'])) {
        $error = 'Current password is incorrect.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New passwords do not match.';
    } elseif (strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters.';
    } else {
        try {
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $id]);
            $message = 'Password changed successfully!';
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Admin Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h5><i class="bi bi-gear"></i> Settings</h5>
                <p>Manage system settings</p>
            </div>
            <nav>
                <ul class="admin-nav">
                    <li class="admin-nav-item">
                        <a href="dashboard.php" class="admin-nav-link">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="enquiries.php" class="admin-nav-link">
                            <i class="bi bi-envelope"></i> Enquiries
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="cars.php" class="admin-nav-link">
                            <i class="bi bi-ev-front"></i> Cars
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="users.php" class="admin-nav-link">
                            <i class="bi bi-people"></i> Users
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="settings.php" class="admin-nav-link active">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    </li>
                </ul>
            </nav>
            <hr class="admin-nav-divider">
            <ul class="admin-nav">
                <li class="admin-nav-item">
                    <a href="profile.php" class="admin-nav-link">
                        <i class="bi bi-person-circle"></i> My Profile
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="../logout.php" class="admin-nav-link">
                        <i class="bi bi-box-arrow-left"></i> Logout
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="admin-main-content">
            <div class="admin-page-header">
                <div>
                    <h1 class="admin-page-title"><i class="bi bi-gear"></i> Settings</h1>
                    <p class="admin-page-subtitle">Manage your account and system settings</p>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="admin-alert admin-alert-success">
                    <i class="bi bi-check-circle"></i> <?= $message ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="admin-alert admin-alert-danger">
                    <i class="bi bi-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <!-- Admin Profile Section -->
            <div class="admin-form-card">
                <div class="admin-form-header">
                    <h2><i class="bi bi-person-circle"></i> Your Profile</h2>
                </div>
                <div class="admin-profile-info">
                    <div class="admin-profile-row">
                        <span class="admin-profile-label">Email Address:</span>
                        <span class="admin-profile-value"><?= htmlspecialchars($admin['email']) ?></span>
                    </div>
                    <div class="admin-profile-row">
                        <span class="admin-profile-label">Full Name:</span>
                        <span class="admin-profile-value"><?= htmlspecialchars($admin['full_name'] ?: 'Not set') ?></span>
                    </div>
                    <div class="admin-profile-row">
                        <span class="admin-profile-label">Phone Number:</span>
                        <span class="admin-profile-value"><?= htmlspecialchars($admin['phone'] ?: 'Not set') ?></span>
                    </div>
                    <div class="admin-profile-row">
                        <span class="admin-profile-label">Member Since:</span>
                        <span class="admin-profile-value"><?= date('d M Y', strtotime($admin['created_at'])) ?></span>
                    </div>
                    <div class="admin-profile-row">
                        <span class="admin-profile-label">Role:</span>
                        <span class="admin-profile-value">
                            <span class="admin-table-badge" style="background: var(--danger-light); color: var(--danger-dark);">
                                <i class="bi bi-shield-exclamation"></i> Administrator
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Change Password Section -->
            <div class="admin-form-card">
                <div class="admin-form-header">
                    <h2><i class="bi bi-lock"></i> Change Password</h2>
                </div>
                <form method="post" class="admin-form">
                    <input type="hidden" name="change_password" value="1">
                    
                    <div class="admin-form-row">
                        <div class="admin-form-col">
                            <label for="current_password" class="admin-form-label">Current Password</label>
                            <input type="password" class="admin-form-input" id="current_password" name="current_password" required>
                        </div>
                    </div>

                    <div class="admin-form-row">
                        <div class="admin-form-col">
                            <label for="new_password" class="admin-form-label">New Password</label>
                            <input type="password" class="admin-form-input" id="new_password" name="new_password" required>
                            <small class="admin-form-hint">Minimum 6 characters</small>
                        </div>
                        <div class="admin-form-col">
                            <label for="confirm_password" class="admin-form-label">Confirm Password</label>
                            <input type="password" class="admin-form-input" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>

                    <div class="admin-form-actions">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="bi bi-check"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- System Information Section -->
            <div class="admin-form-card">
                <div class="admin-form-header">
                    <h2><i class="bi bi-info-circle"></i> System Information</h2>
                </div>
                <div class="admin-profile-info">
                    <div class="admin-profile-row">
                        <span class="admin-profile-label">Site Name:</span>
                        <span class="admin-profile-value"><?= SITE_NAME ?></span>
                    </div>
                    <div class="admin-profile-row">
                        <span class="admin-profile-label">Site URL:</span>
                        <span class="admin-profile-value"><code><?= SITE_URL ?></code></span>
                    </div>
                    <div class="admin-profile-row">
                        <span class="admin-profile-label">Company Email:</span>
                        <span class="admin-profile-value"><a href="mailto:<?= COMPANY_EMAIL ?>"><?= COMPANY_EMAIL ?></a></span>
                    </div>
                    <div class="admin-profile-row">
                        <span class="admin-profile-label">PHP Version:</span>
                        <span class="admin-profile-value"><code><?= phpversion() ?></code></span>
                    </div>
                    <div class="admin-profile-row">
                        <span class="admin-profile-label">Server Time:</span>
                        <span class="admin-profile-value"><?= date('d M Y H:i:s') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="admin-sidebar-toggle" type="button" id="adminSidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>

    <style>
        .admin-profile-info {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        
        .admin-profile-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            align-items: center;
            gap: 20px;
            padding: 14px 12px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .admin-profile-row:last-child {
            border-bottom: none;
        }
        
        .admin-profile-label {
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 14px;
        }
        
        .admin-profile-value {
            color: var(--text-primary);
            word-break: break-word;
        }
        
        .admin-profile-value code {
            background: var(--bg-light);
            padding: 4px 8px;
            border-radius: 4px;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 13px;
        }
        
        .admin-profile-value a {
            color: var(--primary-blue);
            text-decoration: none;
        }
        
        .admin-profile-value a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .admin-profile-row {
                grid-template-columns: 1fr;
                gap: 8px;
                padding: 12px 12px;
            }
            
            .admin-profile-label {
                font-weight: 700;
                display: inline;
            }
            
            .admin-profile-value::before {
                content: " ";
            }
        }
    </style>

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