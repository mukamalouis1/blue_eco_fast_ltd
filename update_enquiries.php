<?php
require 'includes/config.php';

try {
    $pdo = getDB();

    // Add status and response columns to enquiries table
    $pdo->exec("ALTER TABLE enquiries ADD COLUMN status VARCHAR(20) DEFAULT 'pending'");
    $pdo->exec("ALTER TABLE enquiries ADD COLUMN response TEXT");
    $pdo->exec("ALTER TABLE enquiries ADD COLUMN response_date TIMESTAMP NULL");

    echo "Enquiries table updated successfully!\n";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column") !== false) {
        echo "Columns already exist.\n";
    } else {
        die('Update failed: ' . $e->getMessage());
    }
}
?>