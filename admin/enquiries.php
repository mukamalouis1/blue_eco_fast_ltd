<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
requireAdmin();

$pdo = getDB();

// Handle bulk status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_update'])) {
    $enquiry_ids = $_POST['enquiry_ids'] ?? [];
    $bulk_status = clean($_POST['bulk_status'] ?? '');

    if (!empty($enquiry_ids) && !empty($bulk_status)) {
        try {
            $placeholders = str_repeat('?,', count($enquiry_ids) - 1) . '?';
            $stmt = $pdo->prepare("UPDATE enquiries SET status = ? WHERE id IN ($placeholders)");
            $stmt->execute(array_merge([$bulk_status], $enquiry_ids));
            $message = "Bulk update completed successfully!";
        } catch (PDOException $e) {
            $error = "Bulk update failed: " . $e->getMessage();
        }
    }
}

// Get enquiry if viewing details
$enquiry = null;
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM enquiries WHERE id = ?");
    $stmt->execute([$id]);
    $enquiry = $stmt->fetch();
}

// Get enquiries with optional status filter
$statusFilter = $_GET['status'] ?? '';
$query = "SELECT * FROM enquiries";
$params = [];

if ($statusFilter) {
    $query .= " WHERE status = ?";
    $params[] = $statusFilter;
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$enquiries = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Enquiries - Admin</title>
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
                        <a href="enquiries.php" class="admin-nav-link active">
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
        <main class="admin-main-content">
            <?php if (isset($message)): ?>
                <div class="admin-alert admin-alert-success">
                    <i class="bi bi-check-circle"></i>
                    <div><?= $message ?></div>
                </div>
            <?php endif; if (isset($error)): ?>
                <div class="admin-alert admin-alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    <div><?= $error ?></div>
                </div>
            <?php endif; ?>

            <?php if ($enquiry): ?>
                <!-- Enquiry Detail View -->
                <div class="admin-page-header">
                    <h1 class="admin-page-title">
                        <i class="bi bi-envelope-open"></i> Enquiry Details
                    </h1>
                    <p class="admin-page-subtitle">Review and respond to customer enquiry</p>
                </div>

                <div class="admin-card">
                    <div class="admin-card-title">
                        <i class="bi bi-person-check"></i> Customer Information
                    </div>
                    <div class="admin-form-row">
                        <div>
                            <label class="admin-form-label">Full Name</label>
                            <p class="admin-card-body"><?= htmlspecialchars($enquiry['full_name']) ?></p>
                        </div>
                        <div>
                            <label class="admin-form-label">Email Address</label>
                            <p class="admin-card-body"><?= htmlspecialchars($enquiry['email']) ?></p>
                        </div>
                        <div>
                            <label class="admin-form-label">Phone Number</label>
                            <p class="admin-card-body"><?= htmlspecialchars($enquiry['phone']) ?></p>
                        </div>
                        <div>
                            <label class="admin-form-label">Date</label>
                            <p class="admin-card-body"><?= date('d M Y H:i', strtotime($enquiry['created_at'])) ?></p>
                        </div>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="admin-card-title">
                        <i class="bi bi-file-earmark-text"></i> Enquiry Details
                    </div>
                    <div class="admin-form-row">
                        <div>
                            <label class="admin-form-label">Service Interested</label>
                            <p class="admin-card-body"><?= htmlspecialchars($enquiry['service']) ?></p>
                        </div>
                        <div>
                            <label class="admin-form-label">Budget</label>
                            <p class="admin-card-body"><?= htmlspecialchars($enquiry['budget'] ?: 'Not specified') ?></p>
                        </div>
                    </div>

                    <div style="margin-top: var(--space-xl);">
                        <label class="admin-form-label">Preferred Cars</label>
                        <p class="admin-card-body"><?= nl2br(htmlspecialchars($enquiry['cars'])) ?></p>
                    </div>

                    <div style="margin-top: var(--space-xl);">
                        <label class="admin-form-label">Customer Message</label>
                        <p class="admin-card-body"><?= nl2br(htmlspecialchars($enquiry['message'] ?: '—')) ?></p>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="admin-card-title">
                        <i class="bi bi-pencil-square"></i> Update Status & Response
                    </div>
                    <form method="post">
                        <input type="hidden" name="enquiry_id" value="<?= $enquiry['id'] ?>">
                        
                        <div class="admin-form-group">
                            <label for="status" class="admin-form-label required">Status</label>
                            <select class="admin-form-select" id="status" name="status">
                                <option value="pending" <?= ($enquiry['status'] ?? 'pending') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="contacted" <?= ($enquiry['status'] ?? 'pending') === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                                <option value="in-progress" <?= ($enquiry['status'] ?? 'pending') === 'in-progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="responded" <?= ($enquiry['status'] ?? 'pending') === 'responded' ? 'selected' : '' ?>>Responded</option>
                                <option value="completed" <?= ($enquiry['status'] ?? 'pending') === 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="closed" <?= ($enquiry['status'] ?? 'pending') === 'closed' ? 'selected' : '' ?>>Closed</option>
                            </select>
                        </div>

                        <div class="admin-form-group">
                            <label for="response" class="admin-form-label">Admin Response</label>
                            <textarea class="admin-form-textarea" id="response" name="response"><?= htmlspecialchars($enquiry['response'] ?? '') ?></textarea>
                            <div class="admin-form-help">Add your response or notes about this enquiry</div>
                        </div>

                        <div class="admin-actions" style="justify-content: flex-start; gap: var(--space-lg);">
                            <button type="submit" class="admin-btn admin-btn-primary">
                                <i class="bi bi-save"></i> Update Enquiry
                            </button>
                            <a href="enquiries.php" class="admin-btn admin-btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <!-- Enquiries List -->
                <div class="admin-page-header">
                    <h1 class="admin-page-title">
                        <i class="bi bi-envelope"></i> Manage Enquiries
                    </h1>
                    <p class="admin-page-subtitle">View and manage all customer enquiries</p>
                </div>

                <!-- Filters -->
                <div class="admin-card" style="margin-bottom: var(--space-2xl);">
                    <form method="get" class="d-flex gap-2 align-items-end">
                        <div style="flex: 1;">
                            <label class="admin-form-label">Filter by Status</label>
                            <select class="admin-form-select" name="status" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="contacted" <?= $statusFilter === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                                <option value="in-progress" <?= $statusFilter === 'in-progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="responded" <?= $statusFilter === 'responded' ? 'selected' : '' ?>>Responded</option>
                                <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="closed" <?= $statusFilter === 'closed' ? 'selected' : '' ?>>Closed</option>
                            </select>
                        </div>
                        <?php if ($statusFilter): ?>
                            <a href="enquiries.php" class="admin-btn admin-btn-secondary">
                                <i class="bi bi-x-circle"></i> Clear Filter
                            </a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Bulk Actions -->
                <form method="post" id="bulkForm">
                    <div class="admin-card" style="margin-bottom: var(--space-lg);">
                        <div style="display: grid; grid-template-columns: auto auto; gap: var(--space-lg); align-items: center;">
                            <div>
                                <label class="admin-form-label">Bulk Update Status</label>
                                <select class="admin-form-select" name="bulk_status" id="bulkStatus" style="width: 250px;">
                                    <option value="">Select Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="contacted">Contacted</option>
                                    <option value="in-progress">In Progress</option>
                                    <option value="responded">Responded</option>
                                    <option value="completed">Completed</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <button type="submit" name="bulk_update" class="admin-btn admin-btn-primary" id="bulkBtn" disabled>
                                <i class="bi bi-check-circle"></i> Update Selected
                            </button>
                        </div>
                    </div>

                    <!-- Enquiries Table -->
                    <div class="admin-table-container">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th width="40"><input type="checkbox" id="selectAll"></th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Service</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enquiries as $enq): ?>
                                    <tr>
                                        <td><input type="checkbox" name="enquiry_ids[]" value="<?= $enq['id'] ?>" form="bulkForm"></td>
                                        <td><strong><?= htmlspecialchars($enq['full_name']) ?></strong></td>
                                        <td><?= htmlspecialchars($enq['email']) ?></td>
                                        <td><?= htmlspecialchars($enq['service']) ?></td>
                                        <td>
                                            <?php
                                            $status = $enq['status'] ?? 'pending';
                                            $statusBadgeClass = 'status-' . $status;
                                            ?>
                                            <span class="admin-table-badge <?= $statusBadgeClass ?>"><?= ucfirst(str_replace('-', ' ', $status)) ?></span>
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
                    </div>
                </form>
            <?php endif; ?>
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
            if (toggle && sidebar) toggle.addEventListener('click', () => sidebar.classList.toggle('active'));
        })();

        // Bulk selection functionality (list view only)
        const selectAll = document.getElementById('selectAll');
        const bulkForm = document.getElementById('bulkForm');
        const bulkBtn = document.getElementById('bulkBtn');
        const bulkStatusEl = document.getElementById('bulkStatus');

        function updateBulkButton() {
            if (!bulkBtn) return;
            const checkedBoxes = document.querySelectorAll('input[name="enquiry_ids[]"]:checked');
            bulkBtn.disabled = checkedBoxes.length === 0;
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('input[name="enquiry_ids[]"]');
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkButton();
            });

            document.addEventListener('change', function(e) {
                if (e.target && e.target.name === 'enquiry_ids[]') {
                    updateBulkButton();
                    const checkboxes = document.querySelectorAll('input[name="enquiry_ids[]"]');
                    const checkedBoxes = document.querySelectorAll('input[name="enquiry_ids[]"]:checked');
                    selectAll.checked = checkboxes.length === checkedBoxes.length && checkboxes.length > 0;
                    selectAll.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < checkboxes.length;
                }
            });
        }

        if (bulkForm) {
            bulkForm.addEventListener('submit', function(e) {
                const bulkStatus = bulkStatusEl ? bulkStatusEl.value : '';
                if (!bulkStatus) {
                    e.preventDefault();
                    alert('Please select a status for bulk update.');
                    return;
                }
                const checkedBoxes = document.querySelectorAll('input[name="enquiry_ids[]"]:checked');
                if (checkedBoxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one enquiry.');
                }
            });
        }
    </script>
</body>
</html>