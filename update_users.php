<?php
require 'includes/config.php';

try {
    $pdo = getDB();

    // Add new columns to users table
    $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('admin', 'user') DEFAULT 'user'");
    $pdo->exec("ALTER TABLE users ADD COLUMN email_verified TINYINT(1) DEFAULT 0");
    $pdo->exec("ALTER TABLE users ADD COLUMN email_verification_token VARCHAR(255) NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN email_verification_expires TIMESTAMP NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN password_reset_token VARCHAR(255) NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN password_reset_expires TIMESTAMP NULL");

    // Set existing users as verified and admin role for first user
    $pdo->exec("UPDATE users SET email_verified = 1, role = 'admin' WHERE id = 1");

    echo "Users table updated successfully!\n";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column") !== false) {
        echo "Columns already exist.\n";
    } else {
        die('Update failed: ' . $e->getMessage());
    }
}
?>