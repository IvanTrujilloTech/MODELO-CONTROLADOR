<?php
// este archivo contiene funciones ayudantes para la seguridad
// funciones utilitarias para validacion, sanitizacion y proteccion

// funcion que valida emails
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// funcion que valida nombres (solo letras y espacios)
function validarNombre($nombre) {
    return preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}$/", $nombre);
}

// funcion que valida contraseñas (min 8 caracteres, mayuscula, minuscula, numero)
function validarPassword($password) {
    return preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/", $password);
}

// funcion que valida numeros decimales (para montos)
function validarMonto($monto) {
    return is_numeric($monto) && $monto > 0 && $monto <= 999999.99;
}

// funcion que sanitiza entradas HTML
function sanitizarHTML($input) {
    return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
}

// funcion que genera tokens CSRF
function generarCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

// funcion que valida tokens CSRF
function validarCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    // token valido por 1 hora
    $tokenAge = time() - $_SESSION['csrf_token_time'];
    if ($tokenAge > 3600) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

// funcion que verifica roles de usuario
function verificarRol($rolRequerido) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
        return false;
    }
    
    return $_SESSION['user_role'] === $rolRequerido;
}

// funcion que protege contra XSS al mostrar datos en HTML
function escaparHTML($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// funcion que genera una cadena aleatoria segura
function generarTokenSeguro($longitud = 32) {
    return bin2hex(random_bytes($longitud / 2));
}

// funcion que valida fechas en formato YYYY-MM-DD
function validarFecha($fecha) {
    $d = DateTime::createFromFormat('Y-m-d', $fecha);
    return $d && $d->format('Y-m-d') === $fecha;
}

// funcion que valida categorias de transacciones
function validarCategoria($categoria) {
    $categoriasValidas = ['alimentacion', 'transporte', 'vivienda', 'entretenimiento', 'salud', 'educacion', 'ahorro', 'inversiones', 'otros'];
    return in_array(strtolower($categoria), $categoriasValidas);
}

// funcion que valida tipos de transacciones
function validarTipoTransaccion($tipo) {
    return in_array(strtolower($tipo), ['ingreso', 'gasto']);
}

// incluir helper de webhook
require_once __DIR__ . '/WebhookHelper.php';
?>