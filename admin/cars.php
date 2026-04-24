<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
requireAdmin();

$pdo = getDB();
$message = $error = '';

// Define dropdown options
$categories = ['Sedan', 'SUV', 'Hatchback', 'Van', 'Luxury', 'Truck'];
$types = ['Luxury', 'Economy', 'Family', 'Sports'];
$fuel_types = ['Electric', 'Hybrid', 'Petrol', 'Diesel'];

// Handle add/edit car
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name'] ?? '');
    $category = clean($_POST['category'] ?? '');
    $type = clean($_POST['type'] ?? '');
    $range_km = clean($_POST['range_km'] ?? '');
    $seats = (int)($_POST['seats'] ?? 0);
    $price = clean($_POST['price'] ?? '');
    $fuel_type = clean($_POST['fuel_type'] ?? '');
    $car_id = isset($_POST['car_id']) ? (int)$_POST['car_id'] : null;
    
    // Image Upload Logic
    $image_path = $_POST['existing_image'] ?? ''; 
    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['car_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newName = time() . '_' . $filename;
            $uploadDir = __DIR__ . '/../uploads/cars/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            if (move_uploaded_file($_FILES['car_image']['tmp_name'], $uploadDir . $newName)) {
                $image_path = 'uploads/cars/' . $newName;
            }
        } else {
            $error = "Invalid image format. Use JPG, PNG or WebP.";
        }
    }

    if (!$error) {
        try {
            if ($car_id) {
                $stmt = $pdo->prepare("UPDATE cars SET image=?, name=?, category=?, type=?, range_km=?, seats=?, price=?, fuel_type=? WHERE id=?");
                $stmt->execute([$image_path, $name, $category, $type, $range_km, $seats, $price, $fuel_type, $car_id]);
                $message = "Car updated successfully!";
            } else {
                $stmt = $pdo->prepare("INSERT INTO cars (image, name, category, type, range_km, seats, price, fuel_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$image_path, $name, $category, $type, $range_km, $seats, $price, $fuel_type]);
                $message = "Car added successfully!";
            }
            // Refresh car list
            $car = null; 
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $pdo->prepare("DELETE FROM cars WHERE id=?")->execute([$id]);
        $message = "Car deleted successfully!";
    } catch (PDOException $e) {
        $error = "Delete failed: " . $e->getMessage();
    }
}

$cars = $pdo->query("SELECT * FROM cars ORDER BY name")->fetchAll();

$car = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id=?");
    $stmt->execute([$id]);
    $car = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cars - Admin</title>
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
                <h5><i class="bi bi-ev-front"></i> Cars</h5>
                <p>Manage vehicle inventory</p>
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
                        <a href="cars.php" class="admin-nav-link active">
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
        <div class="admin-main-content">
            <div class="admin-page-header">
                <div>
                    <h1 class="admin-page-title"><i class="bi bi-ev-front"></i> Manage Cars</h1>
                    <p class="admin-page-subtitle">Add, edit, and manage vehicle inventory</p>
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

            <!-- Add/Edit Car Form -->
            <div class="admin-form-card">
                <div class="admin-form-header">
                    <h2><?= $car ? 'Update Car Details' : 'Add New Car' ?></h2>
                </div>
                <form method="post" enctype="multipart/form-data" class="admin-form">
                    <?php if ($car): ?>
                        <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
                        <input type="hidden" name="existing_image" value="<?= $car['image'] ?>">
                    <?php endif; ?>
                    
                    <div class="admin-form-row">
                        <div class="admin-form-col">
                            <label class="admin-form-label">Car Image</label>
                            <input type="file" class="admin-form-input" name="car_image" <?= $car ? '' : 'required' ?> accept="image/jpeg,image/png,image/webp">
                            <?php if ($car && $car['image']): ?>
                                <small class="admin-form-hint">Current: <?= basename($car['image']) ?></small>
                            <?php endif; ?>
                        </div>
                        <div class="admin-form-col">
                            <label class="admin-form-label">Car Name</label>
                            <input type="text" class="admin-form-input" name="name" value="<?= htmlspecialchars($car['name'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="admin-form-row">
                        <div class="admin-form-col">
                            <label class="admin-form-label">Category</label>
                            <select class="admin-form-select" name="category" required>
                                <option value="">Select Category</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat ?>" <?= ($car['category'] ?? '') == $cat ? 'selected' : '' ?>><?= $cat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="admin-form-col">
                            <label class="admin-form-label">Type</label>
                            <select class="admin-form-select" name="type" required>
                                <option value="">Select Type</option>
                                <?php foreach($types as $t): ?>
                                    <option value="<?= $t ?>" <?= ($car['type'] ?? '') == $t ? 'selected' : '' ?>><?= $t ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="admin-form-col">
                            <label class="admin-form-label">Fuel Type</label>
                            <select class="admin-form-select" name="fuel_type" required>
                                <option value="">Select Fuel</option>
                                <?php foreach($fuel_types as $f): ?>
                                    <option value="<?= $f ?>" <?= ($car['fuel_type'] ?? '') == $f ? 'selected' : '' ?>><?= $f ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="admin-form-row">
                        <div class="admin-form-col">
                            <label class="admin-form-label">Range (km)</label>
                            <input type="text" class="admin-form-input" name="range_km" value="<?= htmlspecialchars($car['range_km'] ?? '') ?>" required>
                        </div>
                        <div class="admin-form-col">
                            <label class="admin-form-label">Number of Seats</label>
                            <input type="number" class="admin-form-input" name="seats" value="<?= $car['seats'] ?? '' ?>" required min="1">
                        </div>
                        <div class="admin-form-col">
                            <label class="admin-form-label">Price</label>
                            <input type="text" class="admin-form-input" name="price" value="<?= htmlspecialchars($car['price'] ?? '') ?>" required placeholder="e.g., 45,000,000">
                        </div>
                    </div>

                    <div class="admin-form-actions">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <i class="bi bi-<?= $car ? 'pencil-square' : 'plus-circle' ?>"></i>
                            <?= $car ? 'Update Car' : 'Add Car' ?>
                        </button>
                        <?php if ($car): ?>
                            <a href="cars.php" class="admin-btn admin-btn-secondary">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Cars Table -->
            <div class="admin-table-container">
                <div class="admin-table-header">
                    <h2>Vehicle Inventory (<?= count($cars) ?>)</h2>
                </div>
                <?php if (!empty($cars)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Details</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cars as $c): ?>
                            <tr>
                                <td>
                                    <?php if($c['image']): ?>
                                        <img src="../<?= $c['image'] ?>" alt="<?= htmlspecialchars($c['name']) ?>" style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <span class="text-muted"><i class="bi bi-image"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                                <td>
                                    <span class="admin-table-badge" style="background: var(--primary-light); color: var(--primary-dark);">
                                        <?= htmlspecialchars($c['category']) ?>
                                    </span>
                                </td>
                                <td><strong><?= htmlspecialchars($c['price']) ?></strong></td>
                                <td>
                                    <small class="d-block text-muted">Type: <?= htmlspecialchars($c['type']) ?></small>
                                    <small class="d-block text-muted">Fuel: <?= htmlspecialchars($c['fuel_type']) ?></small>
                                    <small class="d-block text-muted">Range: <?= htmlspecialchars($c['range_km']) ?> km</small>
                                </td>
                                <td class="text-center">
                                    <a href="cars.php?edit=<?= $c['id'] ?>" class="admin-btn admin-btn-sm admin-btn-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="cars.php?delete=<?= $c['id'] ?>" class="admin-btn admin-btn-sm admin-btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this car?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="admin-empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No cars in inventory yet. Add one to get started!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
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