<?php

require_once 'Model.php';
require_once '../config/db.php';

class Favoritos extends Model {
    // Anunci hereda la conexión a la base de datos de la clase Model
    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    // Query SQL para obtener favoritos
    public function obtenerFavoritosPorUsuario($usuari_id){
        $stmt = $this->pdo->prepare('SELECT * from user_favorites where usuari_id = ?');
        $stmt->execute([$usuari_id]); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Query SQL para eliminar un favorito
        public function eliminar($id, $usuari_id) {
            $stmt = $this->pdo->prepare("DELETE FROM user_favorites WHERE id = ? AND usuari_id = ?");
            return $stmt->execute([$id, $usuari_id]);
        }
    
    // Query SQL para añadir elemento a favoritos
    public function añadirAFavoritos($nom, $descripcio, $categoria, $url, $usuari_id) {
        $stmt = $this->pdo->prepare("INSERT INTO user_favorites (nom, descripcio, categoria, url, usuari_id) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$nom, $descripcio, $categoria, $url, $usuari_id]);
    }

    // Query SQL para añadir añadir, marcar como visitado o pendiente
    public function gestionarEstadoVisita($nom, $usuari_id, $visitado) {
        try {
            $this->pdo->beginTransaction();
            
            // verify if it already exists
            $stmt = $this->pdo->prepare("SELECT id FROM user_favorites WHERE nom = ? AND usuari_id = ?");
            $stmt->execute([$nom, $usuari_id]);
            
            if ($stmt->fetch()) {
                // if exists, changes visitado
                $update = $this->pdo->prepare("UPDATE user_favorites SET visitado = ? WHERE nom = ? AND usuari_id = ?");
                $update->execute([$visitado, $nom, $usuari_id]);
            } else {
                // inserts new data
                $insert = $this->pdo->prepare("INSERT INTO user_favorites (nom, usuari_id, visitado, descripcio, categoria, url) VALUES (?, ?, ?, '', 'País', NULL)");
                $insert->execute([$nom, $usuari_id, $visitado]);
            }
            
            $this->pdo->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Error en gestión de estado: " . $e->getMessage());
            return false;
        }
    }

    // obtener visitados
    public function obtenerVisitados($usuari_id) {
        $stmt=$this->pdo->prepare("SELECT * FROM user_favorites WHERE usuari_id = ? AND visitado = 1");
        $stmt->execute([$usuari_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // guardar puntuacion
    public function guardarPuntuacion($id, $puntuacion, $usuari_id) {
        $stmt = $this->pdo->prepare("UPDATE user_favorites SET puntuacion = ? WHERE id = ? AND usuari_id = ?");
        if (!$stmt->execute([$puntuacion, $id, $usuari_id])) {
            error_log("Error al guardar puntuación: " . print_r($stmt->errorInfo(), true));
            return false;
        }
        return $stmt->rowCount() > 0; 
    }
}
?>