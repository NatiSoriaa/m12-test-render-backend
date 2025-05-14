<?php
// config/db.php
// Variables de connexió
$host = 'localhost';
$dbname = 'earth_explorer_db';
$user = 'root';
$pass = '';

    // Establim la connexió amb PDO amb MySQL com a motor de dades
    try {    
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        // Gestió d'errors de PDO on fa una excepció si hi ha un error
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET NAMES 'utf8mb4'");
        
        // Captura d'errors
    } catch (PDOException $e) {
        die("Error de connexió a la base de dades: " . $e->getMessage());
}   


?>