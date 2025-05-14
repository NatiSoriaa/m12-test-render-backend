<?php

require_once '../models/Favoritos.php';
require_once '../config/config.php';

// Definimos la clase FavoritosController que manejará las solicitudes 
class FavoritosController {
    private $favoritosModel;

    // Inicializamos el modelo FavoritosModel
    public function __construct() {
        global $pdo; 
        $this->favoritosModel = new Favoritos($pdo);
    }

    // lista favoritos por usuario
    public function obtenerFavoritos() {
        header('Content-Type: application/json');
        if (session_status() == PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 86400,
                'path' => '/',
                'domain' => 'localhost',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }        
        if (!isset($_SESSION['usuari_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            exit();
        }
        $usuari_id = $_SESSION['usuari_id'];

            try {
                $favoritos = $this->favoritosModel->obtenerFavoritosPorUsuario($usuari_id);
                echo json_encode(['success' => true, 'data' => $favoritos]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al obtener favoritos']);
            }
            exit();
        }


    public function añadirFavorito() {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido', 405);
            }
            
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['nom'], $data['categoria'], $_SESSION['usuari_id'])) {
                throw new Exception('Datos incompletos', 400);
            }
            
            $result = $this->favoritosModel->añadirAFavoritos(
                $data['nom'],
                $data['descripcio'] ?? '',
                $data['categoria'],
                $data['url'] ?? null,
                $_SESSION['usuari_id']
            );
            
            if (!$result) {
                throw new Exception('Error al guardar en base de datos', 500);
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Favorito añadido correctamente'
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Eliminar anunci
    public function eliminarFavorito($id) {
        try {
            // Verify session
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start([
                    'cookie_lifetime' => 86400,
                    'cookie_secure' => false,
                    'cookie_httponly' => true,
                    'cookie_samesite' => 'Lax'
                ]);
            }
            
            if (!isset($_SESSION['usuari_id'])) {
                throw new Exception('Usuario no autenticado', 401);
            }
    
            // Validate input
            if (!is_numeric($id)) {
                throw new Exception('ID inválido', 400);
            }
    
            // Delete from database
            $deleted = $this->favoritosModel->eliminar($id, $_SESSION['usuari_id']);
    
            if (!$deleted) {
                throw new Exception('No se encontró el favorito', 404);
            }
    
            // Clean output and send response
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'message' => 'Favorito eliminado',
                'id' => $id
            ]);
            exit;
    
        } catch (PDOException $e) {
            error_log("Database error: ".$e->getMessage());
            $this->sendErrorResponse('Error en la base de datos', 500);
        } catch (Exception $e) {
            $this->sendErrorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Visitados. por defecto 0
    public function actualizarEstadoVisita() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido', 405);
            }
    
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
    
            if (!isset($_SESSION['usuari_id'])) {
                throw new Exception('Usuario no autenticado', 401);
            }
    
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['nom']) || !isset($input['visitado'])) {
                throw new Exception('Datos incompletos', 400);
            }
    
            $success = $this->favoritosModel->gestionarEstadoVisita(
                $input['nom'],
                $_SESSION['usuari_id'],
                (int)$input['visitado']
            );
    
            if (!$success) {
                throw new Exception('Error al actualizar el estado', 500);
            }
    
            echo json_encode([
                'success' => true,
                'message' => $input['visitado'] ? 'Marcado como visitado' : 'Desmarcado como visitado'
            ]);
    
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => $e->getCode()
            ]);
        }
        exit;
    }

    public function obtenerVisitados() {
        header('Content-Type: application/json');
        if (session_status() == PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 86400,
                'path' => '/',
                'domain' => 'localhost',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }        

        if (!isset($_SESSION['usuari_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            exit();
        }
            try {
                $usuari_id = $_SESSION['usuari_id'];
                $visitados = $this->favoritosModel->obtenerVisitados($usuari_id);
                echo json_encode(['success' => true, 'data' => $visitados]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al obtener favoritos']);
            }
            exit();
        }
        
        private function sendErrorResponse($message, $code = 500) {
        ob_end_clean();
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }
    public function guardarPuntuacion() {
        header('Content-Type: application/json');

        try {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            // Verificar sesión primero
            if (!isset($_SESSION['usuari_id'])) {
                throw new Exception('Debes iniciar sesión para calificar', 401);
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido', 405);
            }
            
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['id'], $data['puntuacion'])) {
                throw new Exception('Datos incompletos', 400);
            }
            
            $result = $this->favoritosModel->guardarPuntuacion(
                $data['id'],  
                $data['puntuacion'],
                $_SESSION['usuari_id']
            );
            
            if (!$result) {
                throw new Exception('Error al guardar en base de datos: ' . $errorInfo[2], 500);
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Puntuacion guardada correctamente',
                'success' => true 
            ]);
            exit;

        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'success' => false
            ]);
        }
    }
}    
?>