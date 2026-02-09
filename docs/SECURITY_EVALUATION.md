# Evaluación de Seguridad

## Resumen de Medidas de Seguridad Implementadas

Este documento describe las medidas de seguridad implementadas en la aplicación de gestión financiera personal.

## 1. Validación de Entrada

### 1.1 Validaciones de Datos

La aplicación incluye validaciones exhaustivas para todos los campos de entrada:

- **Nombres**: Solo letras y espacios (2-50 caracteres)
- **Email**: Formato válido (utilizando filter_var)
- **Contraseñas**: Min 8 caracteres, con mayúsculas, minúsculas y números
- **Montos**: Números positivos (0 < monto ≤ 999,999.99)
- **Fechas**: Formato YYYY-MM-DD válido
- **Categorías**: Valores predefinidos válidos
- **Tipos de Transacción**: Solo "ingreso" o "gasto"

### 1.2 Sanitización

Toda entrada del usuario se sanitiza utilizando:

```php
htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
```

Esto previene ataques XSS al eliminar etiquetas HTML y escapar caracteres especiales.

## 2. Protección contra CSRF

### 2.1 Tokens CSRF

Se implementa protección CSRF usando tokens únicos por sesión:

```php
// Generar token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$_SESSION['csrf_token_time'] = time();

// Validar token
function validarCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    $tokenAge = time() - $_SESSION['csrf_token_time'];
    if ($tokenAge > 3600) {
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}
```

### 2.2 Uso en Formularios

Todos los formularios incluyen tokens CSRF:

```html
<input type="hidden" name="csrf_token" value="<?php echo generarCSRFToken(); ?>">
```

## 3. Protección contra SQL Injection

### 3.1 Preparación de Consultas

Se utiliza PDO con consultas preparadas para todas las operaciones de base de datos:

```php
$query = "SELECT * FROM movimientos WHERE usuario_id = ? AND descripcion LIKE ?";
$stmt = $this->db->prepare($query);
$stmt->bindParam(1, $user_id);
$stmt->bindParam(2, $termino);
$stmt->execute();
```

### 3.2 Parámetros Vinculados

Nunca se concatena directamente entrada del usuario en consultas SQL.

## 4. Control de Sesiones

### 4.1 Configuración Segura de Sesiones

```php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // 1 en producción con HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', 1800); // 30 minutos
ini_set('session.cookie_lifetime', 0);
```

### 4.2 Tiempo de Inactividad

Se verifica el tiempo de inactividad para cerrar sesiones automáticamente:

```php
if (isset($_SESSION['login_time'])) {
    $inactivityTime = time() - $_SESSION['login_time'];
    if ($inactivityTime > 1800) { // 30 minutos
        session_destroy();
        header("Location: /login?error=timeout");
        exit;
    }
    $_SESSION['login_time'] = time();
}
```

### 4.3 Regeneración de ID de Sesión

Se regenera el ID de sesión al iniciar sesión:

```php
session_regenerate_id(true);
```

## 5. Control de Roles y Permisos

### 5.1 Verificación de Roles

```php
function verificarRol($rolRequerido) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
        return false;
    }
    
    return $_SESSION['user_role'] === $rolRequerido;
}
```

### 5.2 Restringir Acceso

Se verifica el rol antes de acceder a funcionalidades sensibles:

```php
if (!verificarRol('admin')) {
    header("Location: /dashboard");
    exit;
}
```

## 6. Manejo de Contraseñas

### 6.1 Hash de Contraseñas

Se utiliza password_hash con el algoritmo default (bcrypt):

```php
password_hash($password, PASSWORD_DEFAULT);
```

### 6.2 Verificación de Contraseñas

Se verifica el hash con password_verify:

```php
password_verify($inputPassword, $hashedPassword);
```

## 7. Headers de Seguridad

### 7.1 Headers HTTP

```php
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
```

## 8. Logging de Errores

### 8.1 Registro de Errores

Se implementa logging para errores de webhook:

```php
function logError($mensaje) {
    $logFile = __DIR__ . '/../../logs/webhook_errors.log';
    $linea = date('Y-m-d H:i:s') . " - " . $mensaje . "\n";
    file_put_contents($logFile, $linea, FILE_APPEND);
}
```

## 9. Configuración de Entorno

### 9.1 Variables de Entorno

Se usa getenv() para leer configuraciones:

```php
$webhookUrl = getenv('N8N_WEBHOOK_URL') ?: 'https://n8n.example.com/webhook/your-webhook-id';
```

### 9.2 Base de Datos

Las credenciales de la base de datos se leen de variables de entorno:

```php
$this->host = getenv('DB_HOST') ?: "localhost";
$this->db_name = getenv('DB_NAME') ?: "finanzas_db";
$this->username = getenv('DB_USER') ?: "root";
$this->password = getenv('DB_PASS') ?: "";
```

## 10. Despliegue Seguro

### 10.1 HTTPS

En producción se debe usar HTTPS:

```php
ini_set('session.cookie_secure', 1);
```

### 10.2 Permisos de Archivos

Los archivos sensibles deben tener permisos adecuados:

```bash
chmod 600 .env
chmod 600 config/Database.php
chmod 700 logs/
```

### 10.3 Configuración de Servidor

Para Apache:

```apache
<Directory /var/www/html>
    Options -Indexes
    AllowOverride None
</Directory>

<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

## Evaluación de Riesgos

### Riesgos Aceptables

- **Token CSRF expira en 1 hora**: Es un equilibrio entre seguridad y usabilidad.
- **Session timeout en 30 minutos**: Aceptable para una aplicación de finanzas.
- **Log de errores locales**: Aceptable para debugging.

### Mejoras Futuras

1. Implementar dos factores de autenticación (2FA)
2. Encriptar datos sensibles en la base de datos
3. Implementar monitorización de actividades
4. Añadir captcha en formularios de login/registro
5. Implementar rate limiting para prevender brute force

## Conclusión

La aplicación implementa medidas de seguridad adecuadas para una aplicación de gestión financiera personal, incluyendo:

- Validación y sanitización de entrada
- Protección CSRF
- Prevención de SQL injection
- Control de sesiones seguras
- Control de roles y permisos
- Manejo seguro de contraseñas
- Headers de seguridad
- Logging de errores

Estas medidas proporcionan una base segura para el uso de la aplicación.
