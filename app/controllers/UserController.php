<?php
// este archivo contiene el controlador para la gestion de usuarios
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';

// clase que maneja el registro login y listado de usuarios
class UserController {
    private $db;
    private $usuario;

    public function __construct() {
        $database = new database();
        $this->db = $database->getconnection();
        $this->usuario = new usuario($this->db);
    }

    // registro de usuario con proteccion csrf y validacion
    public function register() {
        if($_POST) {
            // validar token CSRF
            if (!validarCSRFToken($_POST['csrf_token'])) {
                echo "Error de seguridad: Token inválido";
                return;
            }

            // validar campos
            $errores = [];
            if (!validarNombre($_POST['nombre'])) {
                $errores[] = "El nombre debe contener solo letras y espacios, 2-50 caracteres";
            }
            if (!validarEmail($_POST['email'])) {
                $errores[] = "El email no es válido";
            }
            if (!validarPassword($_POST['password'])) {
                $errores[] = "La contraseña debe tener min 8 caracteres, mayúscula, minúscula y número";
            }

            if (!empty($errores)) {
                foreach ($errores as $error) {
                    echo $error . "<br>";
                }
                return;
            }

            $this->usuario->nombre = sanitizarHTML($_POST['nombre']);
            $this->usuario->email = sanitizarHTML($_POST['email']);
            $this->usuario->password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            if($this->usuario->emailExists()) {
                echo "El email ya está registrado.";
            } else {
                if ($this->usuario->create()) {
                    security::regenerate_csrf_token();
                    security::log_security_event('user_registered', ['email' => security::mask_for_log($email)]);
                    header("location: /login");
                    exit;
                } else {
                    security::log_security_event('registration_failed', ['email' => security::mask_for_log($email)]);
                    echo "error al registrar usuario.";
                }
            }
        } else {
            // get request - mostramos el formulario
            security::generate_csrf_token();
            require_once __dir__ . '/../views/register.php';
        }
    }

    // login de usuario con csrf y rate limiting
    public function login() {
        if($_POST) {
            // validar token CSRF
            if (!validarCSRFToken($_POST['csrf_token'])) {
                echo "Error de seguridad: Token inválido";
                return;
            }

            $this->usuario->email = sanitizarHTML($_POST['email']);
            $this->usuario->password = $_POST['password'];

            if($this->usuario->login()) {
                // regenerar id de sesion para seguridad
                session_regenerate_id(true);
                $_SESSION['user_id'] = $this->usuario->id;
                $_SESSION['user_name'] = $this->usuario->nombre;
                $_SESSION['user_role'] = $this->usuario->role;
                $_SESSION['login_time'] = time();
                header("Location: /dashboard");
                exit;
            } else {
                rate_limiter::record_failed_attempt($ip);
                security::log_security_event('login_failed', ['email' => security::mask_for_log($email), 'ip' => $ip]);
                $remaining = rate_limiter::get_remaining_attempts($ip);
                echo "credenciales incorrectas. (" . $remaining . " intentos restantes)";
            }
        } else {
            // get request - mostramos el formulario
            security::generate_csrf_token();
            require_once __dir__ . '/../views/login.php';
        }
    }

    // metodo que obtiene y muestra la lista de todos los usuarios
    public function listUsers() {
        // verificar que es admin
        if (!verificarRol('admin')) {
            header("Location: /dashboard");
            exit;
        }

        $stmt = $this->usuario->getAllUsers();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../views/users.php';
    }
}
