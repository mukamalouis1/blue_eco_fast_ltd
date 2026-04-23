<?php
require 'includes/config.php';

try {
    $pdo = getDB();

    // Add role column to users table
    $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user'");

    // Create a default admin user (email: admin@blueeco.rw, password: Admin123)
    $adminEmail = 'admin@blueeco.rw';
    $adminPassword = password_hash('Admin123', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (email, password, full_name, phone, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$adminEmail, $adminPassword, 'Admin', '+250 788 000 000', 'admin']);

    echo "Admin system setup completed!\n";
    echo "Admin email: $adminEmail\n";
    echo "Admin password: Admin123\n";
    echo "Please change this password after first login!\n";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        echo "Admin user already exists.\n";
    } elseif (strpos($e->getMessage(), "Duplicate column") !== false) {
        echo "Role column already exists.\n";
    } else {
        die('Setup failed: ' . $e->getMessage());
    }
}
?>