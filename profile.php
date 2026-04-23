<?php
session_start();
require_once __DIR__ . '/includes/config.php';

// App is admin-only; profile lives in admin area.
if (isset($_SESSION['id'])) {
    header('Location: admin/profile.php');
    exit;
}

header('Location: login.php');
exit;
