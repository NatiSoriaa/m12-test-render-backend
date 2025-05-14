<?php
// setup_database.php

$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'earth_explorer_db';

try {
    // Conexión 
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear DB
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");

    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tabla de usuarios
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuaris (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        contrasenya VARCHAR(255) NOT NULL,
        rol ENUM('admin', 'normal') DEFAULT 'normal'
    )");

    // Usuari y Admin
    $passwordAdmin = password_hash('admin123', PASSWORD_BCRYPT); 
    $passwordNormal = password_hash('user123', PASSWORD_BCRYPT); 
    $stmt = $pdo->prepare("INSERT INTO usuaris (nom, email, contrasenya, rol) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Admin Usuari', 'admin@example.com', $passwordAdmin, 'admin']);
    $stmt->execute(['Normal Usuari', 'user@example.com', $passwordNormal, 'normal']);

    // Favoritos
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        descripcio TEXT,
        categoria VARCHAR(255) NOT NULL,
        usuari_id INT NOT NULL,
        data_afegit DATETIME DEFAULT CURRENT_TIMESTAMP,
        url TEXT,
        visitado TINYINT(1) DEFAULT 0,
        puntuacion TINYINT UNSIGNED DEFAULT 0,
        FOREIGN KEY (usuari_id) REFERENCES usuaris(id),
        UNIQUE KEY (usuari_id, nom)

    )");

    echo "✅ Base de datos y tablas creadas correctamente!";
    header('Location: http://localhost/M12-Proyecto-4-Natalia-Beatriz/frontend/templates/index.html');
    exit;

} catch (PDOException $e) {
    die("❌ Error: " . $e->getMessage());
}
