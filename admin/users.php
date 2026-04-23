<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
requireAdmin();

$pdo = getDB();

// Handle user registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_user'])) {
    $email = clean($_POST['email'] ?? '');
    $full_name = clean($_POST['full_name'] ?? '');
    $phone = clean($_POST['phone'] ?? '');
    $role = clean($_POST['role'] ?? 'user');

    $errors = [];

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if (empty($full_name)) {
        $errors[] = 'Full name is required.';
    }

    if (empty($errors)) {
        try {
            // Generate verification token
            $verificationToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            $defaultPassword = password_hash($email, PASSWORD_DEFAULT); // Email as default password

            $stmt = $pdo->prepare("INSERT INTO users (email, password, full_name, phone, role, email_verification_token, email_verification_expires) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$email, $defaultPassword, $full_name, $phone, $role, $verificationToken, $expiresAt]);

            // Send verification email
            $verificationLink = SITE_URL . "/verify_email.php?token=" . $verificationToken;
            $subject = "Welcome to " . SITE_NAME . " - Please Verify Your Email";
            $htmlBody = "
                <h2>Welcome to " . SITE_NAME . "</h2>
                <p>Hi " . htmlspecialchars($full_name) . ",</p>
                <p>Your account has been created successfully. Your default password is your email address: <strong>" . htmlspecialchars($email) . "</strong></p>
                <p>Please click the link below to verify your email and set a new password:</p>
                <p><a href='" . $verificationLink . "' style='background:#2eb84e;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Verify Email & Set Password</a></p>
                <p><strong>Important:</strong> This link will expire in 10 minutes.</p>
                <p>If the button doesn't work, copy and paste this URL into your browser:<br>
                <a href='" . $verificationLink . "'>" . $verificationLink . "</a></p>
                <p>Best regards,<br>" . COMPANY_NAME . "</p>
            ";
            $plainBody = "Welcome to " . SITE_NAME . "\n\nYour account has been created. Default password: " . $email . "\n\nVerify your email: " . $verificationLink . "\n\nThis link expires in 10 minutes.";

            $mailResult = sendMail($email, $full_name, $subject, $htmlBody, $plainBody);

            if ($mailResult['ok']) {
                $message = "User registered successfully! Verification email sent.";
            } else {
                $message = "User registered but email failed to send: " . $mailResult['error'];
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                $errors[] = 'Email already exists.';
            } else {
                $errors[] = 'Registration failed: ' . $e->getMessage();
            }
        }
    }
}

// Get all users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
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
                <h5><i class="bi bi-envelope"></i> Enquiries</h5>
                <p>Manage messages</p>
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
                        <a href="users.php" class="admin-nav-link active">
                            <i class="bi bi-people"></i> Users
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="settings.php" class="admin-nav-link">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    </li>
                </ul>
            </nav>
            <hr class="admin-nav-divider">
            <ul class="admin-nav">
                <li class="admin-nav-item">
                    <a href="../profile.php" class="admin-nav-link">
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
                    <h1 class="admin-page-title"><i class="bi bi-people"></i> Manage Users</h1>
                    <p class="admin-page-subtitle">Create and manage user accounts</p>
                </div>
                <button class="admin-btn admin-btn-primary" data-bs-toggle="modal" data-bs-target="#registerModal">
                    <i class="bi bi-person-plus"></i> Register New User
                </button>
            </div>

            <?php if (isset($message)): ?>
                <div class="admin-alert admin-alert-success">
                    <i class="bi bi-check-circle"></i> <?= $message ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="admin-alert admin-alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    <div>
                        <?php foreach ($errors as $error): ?>
                            <div><?= $error ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Users Table -->
            <div class="admin-table-container">
                <div class="admin-table-header">
                    <h2>All Users (<?= count($users) ?>)</h2>
                </div>
                <?php if (!empty($users)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Full Name</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Member Since</th>
                                <th class="text-center">Enquiries</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): 
                                // Count user's enquiries
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM enquiries WHERE email = ?");
                                $stmt->execute([$user['email']]);
                                $enquiryCount = $stmt->fetchColumn();
                            ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($user['email']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($user['full_name'] ?: '—') ?></td>
                                <td><?= htmlspecialchars($user['phone'] ?: '—') ?></td>
                                <td>
                                    <span class="admin-table-badge" style="background: <?= $user['role'] === 'admin' ? 'var(--danger-light)' : 'var(--primary-light)' ?>; color: <?= $user['role'] === 'admin' ? 'var(--danger-dark)' : 'var(--primary-dark)' ?>;">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="admin-table-badge" style="background: <?= $user['email_verified'] ? 'var(--success-light)' : 'var(--warning-light)' ?>; color: <?= $user['email_verified'] ? 'var(--success-dark)' : 'var(--warning-dark)' ?>;">
                                        <?= $user['email_verified'] ? 'Verified' : 'Pending' ?>
                                    </span>
                                </td>
                                <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark"><?= $enquiryCount ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="admin-empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No users yet. Register one to get started!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <button class="admin-sidebar-toggle" type="button" id="adminSidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list"></i>
    </button>

    <!-- Registration Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content admin-modal">
                <div class="modal-header admin-modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus"></i> Register New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="admin-form-group">
                            <label for="email" class="admin-form-label">Email Address <span class="required">*</span></label>
                            <input type="email" class="admin-form-input" id="email" name="email" required>
                            <small class="admin-form-hint">This will be the username and default password.</small>
                        </div>
                        <div class="admin-form-group">
                            <label for="full_name" class="admin-form-label">Full Name <span class="required">*</span></label>
                            <input type="text" class="admin-form-input" id="full_name" name="full_name" required>
                        </div>
                        <div class="admin-form-group">
                            <label for="phone" class="admin-form-label">Phone Number</label>
                            <input type="tel" class="admin-form-input" id="phone" name="phone">
                        </div>
                        <div class="admin-form-group">
                            <label for="role" class="admin-form-label">Role</label>
                            <select class="admin-form-select" id="role" name="role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--border-color); padding: 16px;">
                        <button type="button" class="admin-btn admin-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="register_user" class="admin-btn admin-btn-primary">
                            <i class="bi bi-check"></i> Register & Send Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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