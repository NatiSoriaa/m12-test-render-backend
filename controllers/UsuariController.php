<?php

require_once '../models/Usuari.php';
require_once '../config/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start([
        'name' => 'EarthExplorerSess',
        'cookie_lifetime' => 86400, 
        'cookie_secure' => false,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

// Instanciamos la clase UsuariController que gestionará las solicitudes
class UsuariController {
    private $usuariModel;

    // Constructor del modelo Usuari
    public function __construct() {
        $this->usuariModel = new Usuari();

    }
    // Función de registro
    public function registre() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            // datos desde el body json 
            $data = json_decode(file_get_contents("php://input"), true);
            $nom = $data['nom'];
            $email = $data['email'];
            $contrasenya = password_hash($data['contrasenya'], PASSWORD_BCRYPT);

            // Creación de usuario y redirigimos a login
            if ($this->usuariModel->crearUsuari($nom, $email, $contrasenya)) {
                // envia json al frontend
                echo json_encode(['success' => true, 'redirect' =>'M12-Proyecto-4-Natalia-Beatriz/backend/public/index.php?action=login']);
                } else {
                    echo json_encode(['success' => false, 'message' =>'El email ya está registrado en la base de datos.']);
            }
                exit();
          }

    }

    // Método login
    public function login() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // datos desde el frontend
            $data = json_decode(file_get_contents("php://input"), true);
            $email = $data['email'];
            $contrasenya = $data['contrasenya'];
            // Miramos si el email insertado existe en la base de datos
            $usuari = $this->usuariModel->obtenirUsuariPerEmail($email);

            // Si el email y la contraseña coinciden, empieza la sesión
            if ($usuari && password_verify($contrasenya, $usuari['contrasenya'])) {
                session_start();
                $_SESSION['usuari_id'] = $usuari['id'];
                $_SESSION['nom'] = $usuari['nom'];
                $_SESSION['rol'] = $usuari['rol'];
          
                echo json_encode([
                    'success' => true,
                    'id' => $_SESSION['usuari_id'],
                    'nom' => $_SESSION['nom'],
                    'rol' => $_SESSION['rol'],
                ]);
                exit();
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Correo o contraseña incorrectos'
                ]);
                exit();
            }
        }
    }
    
    // check if session is active
    public function checkSession() {
        if (ob_get_length()) ob_clean();
        header("Access-Control-Allow-Origin: http://localhost");
        header("Access-Control-Allow-Credentials: true");
        header('Content-Type: application/json');
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        } 
        if(isset($_SESSION['usuari_id'])) {
            echo json_encode([
                'logged' => true,
                'id' => $_SESSION['usuari_id'],
                'nom' => $_SESSION['nom'],
                'rol' => $_SESSION['rol']
            ]);
        } else {
            echo json_encode([
                'logged' => false
            ]);
        }
        exit();
    }
    // Cerrar la sesión y redirige a login
    public function logout() {
        session_start(); // Iniciar o reanudar sesión 
        session_destroy(); // Cerrar sesión 
        header('Content-Type: application/json'); // responds with json
        echo json_encode(['success' => true, 'message' => 'Sesión cerrada']);
        exit();
    }
}
?>