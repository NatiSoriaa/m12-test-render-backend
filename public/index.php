<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'cookie_secure' => false,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax'
    ]);  
}
define('BASE_URL', 'https://m12-proyecto-4-natalia-beatriz.vercel.app/'); // Define la URL base de la aplicación

// Incluye los controladores necesarios
require_once '../controllers/UsuariController.php';  // Controlador de usuarios
require_once '../controllers/FavoritosController.php';  // Controlador de anuncios
require_once '../config/config.php';

// Obtiene la acción solicitada desde la URL, por defecto es 'index'
$action = $_GET['action'] ?? 'index';  // Usa 'index' si no hay 'action'

// Crea instancias de los controladores
$usuariController = new UsuariController();  // Controlador de usuarios
$favoritosController = new FavoritosController();  // Controlador de anuncios

// Procesa la acción solicitada
switch ($action) {
    case 'register':  // Registra un nuevo usuario
        $usuariController->registre();
        break;
    case 'login':  // Inicia sesión
        $usuariController->login();
        break;

    case 'checkSession':
        $usuariController->checkSession();
        break;
        
    case 'logout':  // Cierra sesión
        $usuariController->logout();
        break;
    case 'añadirFavorito':  // Añadir a favorito
        $favoritosController->añadirFavorito();
        break;
    case 'modificar':  // Modifica un anuncio
        if (isset($_GET['id'])){  // Verifica si 'id' está presente
            $id = $_GET['id'];  // Obtiene el ID del anuncio
            $favoritosController->modificar($id);  // Llama al controlador para modificar
        } else {
            echo 'Id al intentar recuperar el id';  // Error si no se pasa el 'id'
        }
        break;

    case 'eliminarFavorito': //Eliminar favorito
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                throw new Exception('Método no permitido', 405);
            }
            
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['id'])) {
                throw new Exception('ID no proporcionado', 400);
            }
            
            $favoritosController->eliminarFavorito($data['id']);
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);}
    break;
    case 'obtenerFavoritos':  // Obtener favoritos
        $favoritosController->obtenerFavoritos();
        break;
    case 'obtenerVisitados':
        $favoritosController->obtenerVisitados();
        break;
    case 'actualizarEstadoVisita':
        $favoritosController->actualizarEstadoVisita();
        break;
    case 'saveRating':
        $favoritosController->guardarPuntuacion();
        break;
    default:  // Acción por defecto, redirige si no se encuentra ninguna acción
    header('Location: ' . BASE_URL . 'frontend/index.html');
    exit();  // Evita que se siga ejecutando el código
}
?>
