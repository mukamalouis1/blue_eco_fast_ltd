<?php
require_once __DIR__ . '/includes/config.php';

try {
    // Connect to MySQL without database
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // Select the database
    $pdo->exec("USE `" . DB_NAME . "`");

    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100),
            phone VARCHAR(20),
            role ENUM('admin', 'user') DEFAULT 'user',
            email_verified TINYINT(1) DEFAULT 0,
            email_verification_token VARCHAR(255) NULL,
            email_verification_expires TIMESTAMP NULL,
            password_reset_token VARCHAR(255) NULL,
            password_reset_expires TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Create enquiries table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS enquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            service VARCHAR(100) NOT NULL,
            cars TEXT NOT NULL,
            budget VARCHAR(50),
            how_hear VARCHAR(50),
            message TEXT,
            rating INT DEFAULT 0,
            status VARCHAR(20) DEFAULT 'pending',
            response TEXT,
            response_date TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS cars (
            id INT AUTO_INCREMENT PRIMARY KEY,
            image VARCHAR(10) NOT NULL,
            name VARCHAR(100) NOT NULL,
            category VARCHAR(20) NOT NULL,
            type VARCHAR(50) NOT NULL,
            range_km VARCHAR(20) NOT NULL,
            seats INT NOT NULL,
            price VARCHAR(20) NOT NULL,
            fuel_type VARCHAR(10) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Insert cars data
    $cars = [
        ['🚗', 'BYD Atto 3', 'sedan', 'SUV', '420 km', 5, 'USD 28,500', 'EV'],
        ['🚙', 'BYD Seal', 'sedan', 'Sedan', '570 km', 5, 'USD 32,000', 'EV'],
        ['🚐', 'BYD e6', 'van', 'MPV', '400 km', 6, 'USD 35,000', 'EV'],
        ['🚕', 'NETA V', 'taxi', 'Sedan', '401 km', 5, 'USD 18,000', 'EV'],
        ['🚙', 'MG ZS EV', 'suv', 'SUV', '440 km', 5, 'USD 26,500', 'EV'],
        ['🚗', 'NETA S', 'sedan', 'Sedan', '715 km', 5, 'USD 38,000', 'EV'],
        ['🚐', 'BYD T3', 'van', 'Van', '300 km', 7, 'USD 22,000', 'EV'],
        ['🚙', 'Chery Omoda', 'suv', 'SUV', '430 km', 5, 'USD 24,000', 'EV'],
    ];

    $stmt = $pdo->prepare("INSERT INTO cars (image, name, category, type, range_km, seats, price, fuel_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($cars as $car) {
        $stmt->execute($car);
    }

    echo "Database setup completed successfully!\n";
    echo "Tables created: users, cars\n";
    echo "Cars data inserted.\n";

} catch (PDOException $e) {
    die('Database setup failed: ' . $e->getMessage());
}
?>