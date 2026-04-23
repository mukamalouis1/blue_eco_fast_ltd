<?php
session_start();
require_once __DIR__ . '/includes/config.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['id'];
$email = $_SESSION['email'];

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    // Fetch cars for services association
    $cars = $pdo->query("SELECT * FROM cars ORDER BY name")->fetchAll();

    // Fetch user's enquiries
    $stmt = $pdo->prepare("SELECT * FROM enquiries WHERE email = ? ORDER BY created_at DESC");
    $stmt->execute([$email]);
    $enquiries = $stmt->fetchAll();
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?= SITE_NAME ?></a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Welcome, <?= htmlspecialchars($user['full_name'] ?: $email) ?>!</span>
                <a class="nav-link" href="profile.php">My Profile</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Your Dashboard</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Your Details</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                        <p><strong>Full Name:</strong> <?= htmlspecialchars($user['full_name'] ?? 'N/A') ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? 'N/A') ?></p>
                        <p><strong>Member Since:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Associated Services</h5>
                    </div>
                    <div class="card-body">
                        <p>As a registered user, you can:</p>
                        <ul>
                            <li>View detailed car information</li>
                            <li>Submit personalized enquiries</li>
                            <li>Access exclusive offers</li>
                            <li>Track your service requests</li>
                        </ul>
                        <a href="#enquiry" class="btn btn-primary">Get a Quote</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h3>Your Enquiries</h3>
            <?php if (empty($enquiries)): ?>
                <p>You haven't submitted any enquiries yet. <a href="index.php#enquiry">Get a Quote</a></p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Service</th>
                                <th>Cars</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enquiries as $enq): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($enq['created_at']))) ?></td>
                                <td><?= htmlspecialchars($enq['service']) ?></td>
                                <td><?= htmlspecialchars($enq['cars']) ?></td>
                                <td>
                                    <?php
                                    $statusBadge = [
                                        'pending' => 'warning',
                                        'contacted' => 'info',
                                        'in-progress' => 'primary',
                                        'responded' => 'secondary',
                                        'completed' => 'success',
                                        'closed' => 'dark'
                                    ];
                                    $status = $enq['status'] ?? 'pending';
                                    $badgeClass = $statusBadge[$status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst(str_replace('-', ' ', $status)) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>