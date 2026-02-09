<?php
// este archivo contiene el controlador para la gestion de usuarios
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/Usuario.php';

// clase que maneja el registro login y listado de usuarios
class UserController {
    private $db;
    private $usuario;

    // constructor que inicializa la base de datos y el modelo de usuario
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
    }

    // metodo que registra un nuevo usuario verificando si el email ya existe
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

    // metodo que autentica al usuario y establece la sesion
    public function login() {
        if($_POST) {
            $this->usuario->email = $_POST['email'];
            $this->usuario->password = $_POST['password'];

            if($this->usuario->login()) {
                $_SESSION['user_id'] = $this->usuario->id;
                $_SESSION['user_name'] = $this->usuario->nombre;
                $_SESSION['user_role'] = $this->usuario->role;
                header("Location: /dashboard");
                exit;
            } else {
                echo "Credenciales incorrectas.";
            }
        } else {
            require_once __DIR__ . '/../views/login.php';
        }
    }

    // metodo que obtiene y muestra la lista de todos los usuarios
    public function listUsers() {
        $stmt = $this->usuario->getAllUsers();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../views/users.php';
    }
}
?>