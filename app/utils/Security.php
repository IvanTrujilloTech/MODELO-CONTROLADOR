<?php
// clase de utilidades para seguridad
// aqui van funciones para proteger el sistema
class Security {
    
    // tiempo de vida del token csfr
    private const CSRF_TOKEN_LIFETIME = 1800;
    
    // genera un token csrf y lo guarda en sesion
    public static function generate_csrf_token() {
        if (!isset($_SESSION['csrf_token']) || 
            !isset($_SESSION['csrf_token_time']) || 
            (time() - $_SESSION['csrf_token_time']) > self::CSRF_TOKEN_LIFETIME) {
            
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }
    
    // valida un token csrf
    public static function validate_csrf_token($token) {
        if (!isset($_SESSION['csrf_token']) || $token === null) {
            return false;
        }
        
        // comprobamos si el token ha expirado
        if (!isset($_SESSION['csrf_token_time']) || 
            (time() - $_SESSION['csrf_token_time']) > self::CSRF_TOKEN_LIFETIME) {
            self::clear_csrf_token();
            return false;
        }
        
        // comparamos de forma segura para evitar ataques de tiempo
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    // borra el token csrf de la sesion
    public static function clear_csrf_token() {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
    }
    
    // regenera el token csrf despues de una validacion exitosa
    public static function regenerate_csrf_token() {
        self::clear_csrf_token();
        self::generate_csrf_token();
    }
    
    // limpia una cadena para mostrarla de forma segura en html
    public static function sanitize_string($string) {
        if ($string === null) {
            return '';
        }
        
        // quitamos espacios多余的
        $string = trim(preg_replace('/\s+/', ' ', $string));
        
        // convertimos caracteres especiales html
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    // limpia un entero
    public static function sanitize_int($value) {
        return (int) preg_replace('/[^0-9]/', '', (string) $value);
    }
    
    // limpia un numero decimal
    public static function sanitize_float($value) {
        return (float) preg_replace('/[^0-9.]/', '', (string) $value);
    }
    
    // valida un email
    public static function validate_email($email) {
        if ($email === null || empty($email)) {
            return false;
        }
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    // valida una url
    public static function validate_url($url) {
        if ($url === null || empty($url)) {
            return false;
        }
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    // valida que un texto solo tenga caracteres permitidos
    public static function validate_alpha_numeric($string, $pattern = '/^[a-zA-Z0-9\s\-_.,]+$/') {
        if ($string === null) {
            return false;
        }
        return preg_match($pattern, $string) === 1;
    }
    
    // comprueba si el input contiene contenido peligroso
    public static function contains_dangerous_content($input) {
        if ($input === null) {
            return false;
        }
        
        $dangerous_patterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/is',
            '/javascript\s*:/i',
            '/on\w+\s*=/i',
            '/<\s*iframe/is',
            '/<\s*object/is',
            '/<\s*embed/is',
            '/expression\s*\(/i',
            '/data\s*:/i',
        ];
        
        foreach ($dangerous_patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    // hace hash de una contrasena de forma segura
    public static function hash_password($password) {
        return password_hash($password, PASSWORD_DEFAULT | PASSWORD_ARGON2ID);
    }
    
    // verifica una contrasena contra un hash
    public static function verify_password($password, $hash) {
        return password_verify($password, $hash);
    }
    
    // comprueba si la contrasena necesita ser rehasheada
    public static function password_needs_rehash($hash) {
        return password_needs_rehash($hash, PASSWORD_DEFAULT | PASSWORD_ARGON2ID);
    }
    
    // genera un token aleatorio seguro
    public static function generate_secure_token($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    // enmascara datos sensibles para el log
    public static function mask_for_log($data) {
        if (strlen($data) <= 4) {
            return '****';
        }
        return substr($data, 0, 2) . '****' . substr($data, -2);
    }
    
    // valida el origen de la peticion para evitar csrf
    public static function validate_request_origin() {
        if (!isset($_SERVER['HTTP_ORIGIN']) && !isset($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['REQUEST_METHOD'] === 'GET';
        }
        
        $origin = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'];
        $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
        
        $origin_parts = parse_url($origin);
        $host_parts = parse_url('http://' . $host);
        
        return ($origin_parts['host'] ?? '') === ($host_parts['host'] ?? '');
    }
    
    // configura la sesion de forma segura
    public static function configure_secure_session() {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = !in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1']);
            
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            
            session_start();
            
            // regeneramos el id de sesion cada cierto tiempo para evitar fixation
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
            } elseif (time() - $_SESSION['created'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }
    
    // pone los headers de seguridad
    public static function set_security_headers() {
        header('X-XSS-Protection: 1; mode=block');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: sameorigin');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'self';");
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includesubdomains');
        }
    }
    
    // registra eventos de seguridad
    public static function log_security_event($event, $context = []) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'user_id' => $_SESSION['user_id'] ?? 'guest',
            'context' => $context
        ];
        
        error_log(json_encode($log_entry) . PHP_EOL, 3, __DIR__ . '/../../logs/security.log');
    }
    
    // limpia un nombre de archivo para operaciones seguras
    public static function sanitize_filename($filename) {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        $filename = str_replace('..', '_', $filename);
        return substr($filename, 0, 255);
    }
    
    // valida y limpia una url de redireccion
    public static function safe_redirect($url, $default = '/') {
        if ($url === null || empty($url)) {
            return $default;
        }
        
        if (strpos($url, '//') === false || strpos($url, '://' . $_SERVER['HTTP_HOST']) !== false) {
            return $url;
        }
        
        return $default;
    }
}

// clase para limitar intentos y evitar ataques de fuerza bruta
class RateLimiter {
    private static $attempts = [];
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_TIME = 900;
    
    // comprueba si una ip esta bloqueada
    public static function is_locked_out($identifier) {
        if (!isset(self::$attempts[$identifier])) {
            return false;
        }
        
        $attempts = self::$attempts[$identifier];
        
        if ($attempts['count'] >= self::MAX_ATTEMPTS) {
            if (time() - $attempts['last_attempt'] < self::LOCKOUT_TIME) {
                return true;
            } else {
                unset(self::$attempts[$identifier]);
            }
        }
        
        return false;
    }
    
    // registra un intento fallido
    public static function record_failed_attempt($identifier) {
        if (!isset(self::$attempts[$identifier])) {
            self::$attempts[$identifier] = [
                'count' => 0,
                'first_attempt' => time(),
                'last_attempt' => time()
            ];
        }
        
        self::$attempts[$identifier]['count']++;
        self::$attempts[$identifier]['last_attempt'] = time();
    }
    
    // borra los intentos despues de un login exitoso
    public static function clear_attempts($identifier) {
        unset(self::$attempts[$identifier]);
    }
    
    // devuelve los intentos que quedan antes de bloquear
    public static function get_remaining_attempts($identifier) {
        if (!isset(self::$attempts[$identifier])) {
            return self::MAX_ATTEMPTS;
        }
        
        return max(0, self::MAX_ATTEMPTS - self::$attempts[$identifier]['count']);
    }
}
