<?php
session_start();
require_once __DIR__ . '/includes/config.php';

// App is admin-only; send authenticated users to admin dashboard.
if (isset($_SESSION['id'])) {
    header('Location: admin/dashboard.php');
    exit;
}

header('Location: login.php');
exit;
