<?php
$host = getenv("DB_HOST") ?: "localhost";
$dbname = getenv("DB_NAME") ?: "kids_story_world";
$username = getenv("DB_USER") ?: "root";
$password = getenv("DB_PASS") ?: "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $pdo = null;
}
?>
