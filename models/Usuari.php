<?php
// models/Usuari.php

require_once 'Model.php';

// Declaramos la clase Usuari que hereda de Model
class Usuari extends Model {
    
    // Creacion de usuario insertando los datos en la database
    public function crearUsuari($nom, $email, $contrasenya) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO usuaris (nom, email, contrasenya) VALUES (?, ?, ?)");
            return $stmt->execute([$nom, $email, $contrasenya]);
        } catch(PDOException $e) {
            return false;
        }
    }

    // Obtencion del usuario por su email 
    public function obtenirUsuariPerEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuaris WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>