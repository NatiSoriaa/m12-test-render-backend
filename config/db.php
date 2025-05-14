<?php
// config/db.php
// Variables de connexió
$host = getenv('DB_HOST') ?: 'localhost'; // O la IP/nombre del host de Render
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: 'earth_explorer_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8mb4'");
} catch (PDOException $e) {
    die("Error de connexió a la base de dades: " . $e->getMessage());
}
