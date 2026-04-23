<?php
session_start();
require_once __DIR__ . '/includes/config.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id'];
$message = '';
$error = '';

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user) {
        header('Location: login.php');
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

    if (empty($full_name)) {
        $errors[] = 'Full name is required.';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
            $stmt->execute([$full_name, $phone, $id]);

            $message = 'Profile updated successfully!';
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch();
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

    if (empty($current_password)) {
        $errors[] = 'Current password is required.';
    }
    if (empty($new_password) || strlen($new_password) < 6) {
        $errors[] = 'New password must be at least 6 characters.';
    }
    if ($new_password !== $confirm_password) {
        $errors[] = 'New passwords do not match.';
    }
    if (!password_verify($current_password, $user['password'])) {
        $errors[] = 'Current password is incorrect.';
    }

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
    <title>My Profile - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- ════════════════════════════════════════════
         NAVBAR
    ═════════════════════════════════════════════ -->
    <nav class="navbar navbar-bef navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="Blue Eco Fast Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav mx-auto gap-1">
                    <li class="nav-item"><a class="nav-link" href="index.php#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#fleet">Fleet</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#about">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#testimonials">Reviews</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#contact">Contact</a></li>
                </ul>
                <?php if (isset($_SESSION['id'])): ?>
                    <div class="navbar-nav">
                        <span class="navbar-text me-2">Welcome, <?= htmlspecialchars($user['full_name'] ?: $user['email']) ?>!</span>
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                        <a class="nav-link" href="logout.php">Logout</a>
                    </div>
                <?php else: ?>
                    <div class="navbar-nav">
                        <a class="nav-link" href="login.php">Login</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- ════════════════════════════════════════════
         PROFILE CONTENT
    ═════════════════════════════════════════════ -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-5">
                        <h2 class="section-title">My Profile</h2>
                        <div class="section-divider mx-auto"></div>
                        <p class="text-muted">Manage your account information and preferences</p>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle-fill me-2"></i><?= $message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Profile Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profile Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                        <div class="form-text">Email cannot be changed</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="full_name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="member_since" class="form-label">Member Since</label>
                                        <input type="text" class="form-control" value="<?= date('d M Y', strtotime($user['created_at'])) ?>" readonly>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="update_profile" class="btn btn-primary">
                                            <i class="bi bi-save me-2"></i>Update Profile
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="current_password" class="form-label">Current Password *</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label">New Password *</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                                        <div class="form-text">Must be at least 6 characters</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label">Confirm New Password *</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="change_password" class="btn btn-warning">
                                            <i class="bi bi-key me-2"></i>Change Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Account Status -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Account Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                                        <div>
                                            <strong>Email Verified</strong>
                                            <br><small class="text-muted">Your account is active and verified</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-person-badge me-2 fs-5 text-primary"></i>
                                        <div>
                                            <strong>Account Type</strong>
                                            <br><small class="text-muted">Registered User</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="text-center mt-4">
                        <a href="dashboard.php" class="btn btn-outline-primary me-2">
                            <i class="bi bi-speedometer2 me-2"></i>Back to Dashboard
                        </a>
                        <a href="index.php#enquiry" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>New Enquiry
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ════════════════════════════════════════════
         FOOTER
    ═════════════════════════════════════════════ -->
    <footer class="footer-bef">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand">
                        <img src="images/logo.png" alt="Blue Eco Fast">
                        <p>Rwanda's leading electric vehicle mobility company. Sustainable, affordable, and innovative transport for a greener East Africa.</p>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-6">
                    <div class="footer-heading">Quick Links</div>
                    <ul class="footer-links">
                        <li><a href="index.php#home">Home</a></li>
                        <li><a href="index.php#services">Services</a></li>
                        <li><a href="index.php#fleet">Fleet</a></li>
                        <li><a href="index.php#about">About Us</a></li>
                        <li><a href="index.php#testimonials">Reviews</a></li>
                        <li><a href="index.php#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 col-6">
                    <div class="footer-heading">Services</div>
                    <ul class="footer-links">
                        <li><a href="index.php#services">EV Sales</a></li>
                        <li><a href="index.php#services">Vehicle Rental</a></li>
                        <li><a href="index.php#services">Eco Taxi</a></li>
                        <li><a href="index.php#services">Fleet Management</a></li>
                        <li><a href="index.php#services">Corporate Transport</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-heading">Contact Us</div>
                    <ul class="footer-links">
                        <li><a href="tel:+250788000000"><i class="bi bi-telephone me-1"></i>+250 788 000 000</a></li>
                        <li><a href="mailto:info@blueEcoFast.rw"><i class="bi bi-envelope me-1"></i>info@blueEcoFast.rw</a></li>
                        <li><i class="bi bi-geo-alt me-1"></i>Kigali, Rwanda</li>
                        <li><i class="bi bi-clock me-1"></i>Mon–Fri: 8am – 6pm</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© <?= date('Y') ?> <strong style="color:#2eb84e;">Blue Eco Fast Ltd</strong>. All rights reserved. Kigali, Rwanda.</p>
                <p>Designed with 💚 for a greener Rwanda · <em>Reimagine the Ride</em></p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>