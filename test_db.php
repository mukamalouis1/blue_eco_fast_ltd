<?php
require 'includes/config.php';
$pdo = getDB();
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);
?>