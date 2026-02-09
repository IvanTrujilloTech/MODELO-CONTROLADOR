<?php
// controlador para gestionar usuarios
// aqui esta el registro, login y demas cosas de usuarios
class usercontroller {
    private $db;
    private $usuario;

    public function __construct() {
        $database = new database();
        $this->db = $database->getconnection();
        $this->usuario = new usuario($this->db);
    }

    // registro de usuario con proteccion csrf y validacion
    public function register() {
        if ($_server['request_method'] === 'post') {
            // validamos el token csrf
            if (!security::validate_csrf_token($_post['csrf_token'] ?? null)) {
                security::log_security_event('csrf_failure', ['action' => 'register']);
                echo "error de seguridad: token invalido.";
                return;
            }

            // validamos el origen de la peticion
            if (!security::validate_request_origin()) {
                security::log_security_event('origin_failure', ['action' => 'register']);
                echo "error de seguridad: peticion no valida.";
                return;
            }

            // miramos si hay contenido peligroso en el email
            if (security::contains_dangerous_content($_post['email'] ?? '')) {
                security::log_security_event('xss_attempt', ['action' => 'register', 'field' => 'email']);
                echo "error: contenido no permitido.";
                return;
            }

            // comprovamos que esten todos los campos
            $required_fields = ['nombre', 'email', 'password'];
            foreach ($required_fields as $field) {
                if (empty($_post[$field])) {
                    echo "error: el campo {$field} es obligatorio.";
                    return;
                }
            }

            // validamos el formato del email
            if (!security::validate_email($_post['email'])) {
                echo "error: email invalido.";
                return;
            }

            // validamos la longitud de la contrasena
            $password = $_post['password'];
            if (strlen($password) < 8) {
                echo "error: la contrasena debe tener al menos 8 caracteres.";
                return;
            }

            // miramos el rate limiting
            $ip = $_server['remote_addr'];
            if (rate_limiter::is_locked_out($ip)) {
                $remaining = rate_limiter::get_remaining_attempts($ip);
                echo "error: demasiados intentos. intenta mas tarde.";
                return;
            }

            // limpiamos y validamos los datos
            $nombre = security::sanitize_string($_post['nombre']);
            $email = security::sanitize_string($_post['email']);

            // validamos el nombre
            if (!security::validate_alpha_numeric($nombre, '/^[a-za-z0-9\saeiou]{2,100}$/')) {
                echo "error: el nombre contiene caracteres no validos.";
                return;
            }

            // metemos los datos en el usuario
            $this->usuario->nombre = $nombre;
            $this->usuario->email = $email;
            $this->usuario->password = security::hash_password($password);
            $this->usuario->role = 'user';

            // miramos si el email ya existe
            if ($this->usuario->emailexists()) {
                rate_limiter::record_failed_attempt($ip);
                echo "el email ya esta registrado.";
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
        if ($_server['request_method'] === 'post') {
            // validamos el token csrf
            if (!security::validate_csrf_token($_post['csrf_token'] ?? null)) {
                security::log_security_event('csrf_failure', ['action' => 'login']);
                echo "error de seguridad: token invalido.";
                return;
            }

            // validamos el origen
            if (!security::validate_request_origin()) {
                security::log_security_event('origin_failure', ['action' => 'login']);
                echo "error de seguridad: peticion no valida.";
                return;
            }

            // miramos contenido peligroso
            if (security::contains_dangerous_content($_post['email'] ?? '') || 
                security::contains_dangerous_content($_post['password'] ?? '')) {
                security::log_security_event('xss_attempt', ['action' => 'login']);
                echo "error: contenido no permitido.";
                return;
            }

            $ip = $_server['remote_addr'];

            // comprobamos rate limiting
            if (rate_limiter::is_locked_out($ip)) {
                security::log_security_event('login_lockout', ['ip' => $ip]);
                echo "error: demasiados intentos fallidos. tu cuenta esta bloqueada temporalmente.";
                return;
            }

            // comprovamos que esten los campos
            if (empty($_post['email']) || empty($_post['password'])) {
                echo "error: email y contrasena son obligatorios.";
                return;
            }

            // validamos el email
            if (!security::validate_email($_post['email'])) {
                echo "error: email invalido.";
                return;
            }

            // limpiamos el input
            $email = security::sanitize_string($_post['email']);

            // metemos los datos en el usuario
            $this->usuario->email = $email;
            $this->usuario->password = $_post['password'];

            if ($this->usuario->login()) {
                // miramos si la contrasena necesita rehash
                if (security::password_needs_rehash($this->usuario->password)) {
                    $new_hash = security::hash_password($_post['password']);
                    $this->usuario->changepassword($new_hash);
                }

                // regeneramos el id de sesion para evitar fixation
                session_regenerate_id(true);

                // metemos los datos en la sesion
                $_session['user_id'] = $this->usuario->id;
                $_session['user_name'] = $this->usuario->nombre;
                $_session['user_role'] = $this->usuario->role;
                $_session['login_time'] = time();
                $_session['ip'] = $ip;
                $_session['user_agent'] = $_server['http_user_agent'];

                // borramos los intentos fallidos
                rate_limiter::clear_attempts($ip);
                security::regenerate_csrf_token();
                security::log_security_event('login_success', ['user_id' => $this->usuario->id]);

                header("location: /dashboard");
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

    // logout del usuario
    public function logout() {
        // registramos el logout
        if (isset($_session['user_id'])) {
            security::log_security_event('logout', ['user_id' => $_session['user_id']]);
        }

        // borramos todas las variables de sesion
        $_session = [];

        // borramos la cookie de sesion
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // destruimos la sesion
        session_destroy();

        header("location: /login");
        exit;
    }

    // lista todos los usuarios
    public function listusers() {
        // comprovamos que este logueado
        if (!isset($_session['user_id'])) {
            header("location: /login");
            exit;
        }

        // proteccion csrf para este action
        if ($_server['request_method'] === 'post') {
            if (!security::validate_csrf_token($_post['csrf_token'] ?? null)) {
                echo "error de seguridad: token invalido.";
                return;
            }
        }

        $stmt = $this->usuario->getallusers();
        $users = $stmt->fetchall(pdo::fetch_assoc);
        require_once __dir__ . '/../views/users.php';
    }
}
