<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
requireAdmin();

$id = $_SESSION['id'];

try {
    $pdo = getDB();

    // Get stats
    $totalEnquiries = $pdo->query("SELECT COUNT(*) FROM enquiries")->fetchColumn();
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalCars = $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
    $pendingEnquiries = $pdo->query("SELECT COUNT(*) FROM enquiries WHERE status='pending'")->fetchColumn();

    // Get recent enquiries
    $stmt = $pdo->query("SELECT * FROM enquiries ORDER BY created_at DESC LIMIT 10");
    $recentEnquiries = $stmt->fetchAll();
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <h5><i class="bi bi-speedometer2"></i> Admin Panel</h5>
                <p>Dashboard</p>
            </div>
            <nav>
                <ul class="admin-nav">
                    <li class="admin-nav-item">
                        <a href="dashboard.php" class="admin-nav-link active">
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
                        <a href="settings.php" class="admin-nav-link">
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
        <main class="admin-main-content">
            <!-- Page Header -->
            <div class="admin-page-header">
                <h1 class="admin-page-title">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </h1>
                <p class="admin-page-subtitle">Welcome back, Admin! Here's an overview of your platform.</p>
            </div>

            <!-- Stats Grid -->
            <div class="admin-grid">
                <div class="admin-stat-card gradient-bg">
                    <div class="admin-stat-icon"><i class="bi bi-envelope-check"></i></div>
                    <div class="admin-stat-label">Total Enquiries</div>
                    <div class="admin-stat-value"><?= $totalEnquiries ?></div>
                    <div class="admin-stat-trend">All time enquiries</div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-icon"><i class="bi bi-hourglass-split"></i></div>
                    <div class="admin-stat-label">Pending</div>
                    <div class="admin-stat-value" style="color: #fbbf24;"><?= $pendingEnquiries ?></div>
                    <div class="admin-stat-trend">Awaiting response</div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-icon"><i class="bi bi-people"></i></div>
                    <div class="admin-stat-label">Active Users</div>
                    <div class="admin-stat-value"><?= $totalUsers ?></div>
                    <div class="admin-stat-trend">Registered users</div>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-icon"><i class="bi bi-ev-front"></i></div>
                    <div class="admin-stat-label">Fleet Size</div>
                    <div class="admin-stat-value"><?= $totalCars ?></div>
                    <div class="admin-stat-trend">Available cars</div>
                </div>
            </div>

            <!-- Recent Enquiries Table -->
            <div class="admin-table-container">
                <div class="admin-card-title">
                    <i class="bi bi-list-check"></i> Recent Enquiries
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Service</th>
                                <th>Rating</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentEnquiries as $enq): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($enq['full_name']) ?></strong></td>
                                <td><?= htmlspecialchars($enq['email']) ?></td>
                                <td><?= htmlspecialchars($enq['service']) ?></td>
                                <td>
                                    <?php
                                    $stars = '';
                                    for ($i = 0; $i < 5; $i++) {
                                        $stars .= $i < $enq['rating'] ? '★' : '☆';
                                    }
                                    echo '<span style="color: #fbbf24;">' . $stars . '</span>';
                                    ?>
                                </td>
                                <td><?= date('d M Y', strtotime($enq['created_at'])) ?></td>
                                <td>
                                    <div class="admin-actions">
                                        <a href="enquiries.php?id=<?= $enq['id'] ?>" class="admin-btn admin-btn-primary admin-btn-sm">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top: var(--space-lg);">
                    <a href="enquiries.php" class="admin-btn admin-btn-secondary">
                        <i class="bi bi-arrow-right"></i> View All Enquiries
                    </a>
                </div>
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