<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/Usuario.php';

class UserController {
    private $db;
    private $usuario;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
    }

    public function register() {
        if($_POST) {
            $this->usuario->nombre = $_POST['nombre'];
            $this->usuario->email = $_POST['email'];
            $this->usuario->password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            if($this->usuario->emailExists()) {
                echo "El email ya está registrado.";
            } else {
                if($this->usuario->create()) {
                    header("Location: /login");
                    exit;
                } else {
                    echo "Error al registrar usuario.";
                }
            }
        } else {
            require_once __DIR__ . '/../views/register.php';
        }
    }

    public function login() {
        if($_POST) {
            $this->usuario->email = $_POST['email'];
            $this->usuario->password = $_POST['password'];

            if($this->usuario->login()) {
                $_SESSION['user_id'] = $this->usuario->id;
                $_SESSION['user_name'] = $this->usuario->nombre;
                header("Location: /dashboard");
                exit;
            } else {
                echo "Credenciales incorrectas.";
            }
        } else {
            require_once __DIR__ . '/../views/login.php';
        }
    }
}
?>