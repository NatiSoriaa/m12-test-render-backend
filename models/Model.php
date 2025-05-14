<?php
// models/Model.php
require_once __DIR__ . '/../config/db.php';
class Model {
    protected $pdo;

    public function __construct() {
        global $pdo; //acceso a la variable pdo en db.php
        $this->pdo = $pdo;
    }
}
?>