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
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuario = new Usuario($this->db);
    }

    // Registro de usuario con proteccion CSRF y validacion
    public function register() {
        if($_POST) {
            // Validar token CSRF
            if (!validarCSRFToken($_POST['csrf_token'])) {
                echo "Error de seguridad: Token inválido";
                return;
            }

            // Validar campos
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
                    Security::regenerate_csrf_token();
                    Security::log_security_event('user_registered', ['email' => Security::mask_for_log($this->usuario->email)]);
                    header("Location: /login");
                    exit;
                } else {
                    Security::log_security_event('registration_failed', ['email' => Security::mask_for_log($this->usuario->email)]);
                    echo "Error al registrar usuario.";
                }
            }
        } else {
            // GET request - mostramos el formulario
            Security::generate_csrf_token();
            require_once __DIR__ . '/../views/register.php';
        }
    }

    // Login de usuario con CSRF y rate limiting
    public function login() {
        if($_POST) {
            // Validar token CSRF
            if (!validarCSRFToken($_POST['csrf_token'])) {
                echo "Error de seguridad: Token inválido";
                return;
            }

            $this->usuario->email = sanitizarHTML($_POST['email']);
            $this->usuario->password = $_POST['password'];

            $ip = $_SERVER['REMOTE_ADDR'];

            if($this->usuario->login()) {
                // Regenerar id de sesion para seguridad
                session_regenerate_id(true);
                $_SESSION['user_id'] = $this->usuario->id;
                $_SESSION['user_name'] = $this->usuario->nombre;
                $_SESSION['user_role'] = $this->usuario->role;
                $_SESSION['login_time'] = time();
                $_SESSION['ip'] = $ip;
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                header("Location: /dashboard");
                exit;
            } else {
                RateLimiter::record_failed_attempt($ip);
                Security::log_security_event('login_failed', ['email' => Security::mask_for_log($this->usuario->email), 'ip' => $ip]);
                $remaining = RateLimiter::get_remaining_attempts($ip);
                echo "Credenciales incorrectas. (" . $remaining . " intentos restantes)";
            }
        } else {
            // GET request - mostramos el formulario
            Security::generate_csrf_token();
            require_once __DIR__ . '/../views/login.php';
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
