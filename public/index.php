<?php
// punto de entrada de la aplicacion
// configuracion de seguridad y enrutamiento

// errores en produccion (log en vez de mostrar)
error_reporting(e_all);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __dir__ . '/../logs/php_errors.log');

// definimos rutas
define('base_path', dirname(__dir__));
define('app_path', base_path . '/app');
define('public_path', __dir__);

// cargamos autoloader
require_once app_path . '/autoload.php';

// cargamos utilidades de seguridad
require_once app_path . '/utils/security.php';

// iniciamos sesion segura
security::configure_secure_session();

// ponemos headers de seguridad
security::set_security_headers();

// cogemos la ruta actual
$route = isset($_get['url']) ? $_get['url'] : 'home';

// parseamos la ruta
$parts = explode('/', trim($route, '/'));
$controllername = !empty($parts[0]) ? ucfirst($parts[0]) : 'home';
$action = isset($parts[1]) ? $parts[1] : 'index';

// parametros adicionales
$params = array_slice($parts, 2);

// mapeo de rutas
$controllerfile = app_path . '/controllers/' . $controllername . 'controller.php';

// si el controlador no existe
if (!file_exists($controllerfile)) {
    header("http/1.0 404 not found");
    echo "<h1>404 - pagina no encontrada</h1>";
    exit;
}

// cargamos el controlador
require_once $controllerfile;

// instanciamos el controlador
$controllerclass = $controllername . 'controller';
$controller = new $controllerclass();

// si la accion no existe
if (!method_exists($controller, $action)) {
    header("http/1.0 404 not found");
    echo "<h1>404 - accion no encontrada</h1>";
// Carga básica de rutas manual
require_once '../config/Database.php';
require_once '../app/controllers/HomeController.php';
require_once '../app/controllers/UserController.php';
require_once '../app/controllers/DashboardController.php';
require_once '../app/controllers/ChatController.php';
require_once '../app/helpers/SecurityHelper.php';

// configuracion de seguridad para sesiones
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // cambiar a 1 en produccion con HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', 1800); // 30 minutos
ini_set('session.cookie_lifetime', 0);

// headers de seguridad
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

session_start();

// verificar tiempo de inactividad
if (isset($_SESSION['login_time'])) {
    $inactivityTime = time() - $_SESSION['login_time'];
    if ($inactivityTime > 1800) { // 30 minutos
        session_destroy();
        header("Location: /login?error=timeout");
        exit;
    }
    $_SESSION['login_time'] = time();
}

if ($url == '/' || $url == '/index.php') {
    $controller = new HomeController();
    $controller->index();
} elseif ($url == '/register') {
    $controller = new UserController();
    $controller->register();
} elseif ($url == '/login') {
    $controller = new UserController();
    $controller->login();
} elseif ($url == '/dashboard') {
    $controller = new DashboardController();
    $controller->index();
} elseif ($url == '/add-transaction') {
    $controller = new DashboardController();
    $controller->addTransaction();
} elseif ($url == '/logout') {
    session_destroy();
    header("Location: /");
    exit;
} elseif ($url == '/users') {
    $controller = new UserController();
    $controller->listUsers();
} elseif ($url == '/inversiones') {
    $controller = new DashboardController();
    $controller->inversiones();
} elseif ($url == '/comprar-acciones') {
    $controller = new DashboardController();
    $controller->comprarAcciones();
} elseif ($url == '/vender-acciones') {
    $controller = new DashboardController();
    $controller->venderAcciones();
} elseif ($url == '/reset') {
    $controller = new DashboardController();
    $controller->reset();
} elseif ($url == '/chat') {
    $controller = new ChatController();
    $controller->index();
} elseif ($url == '/transfer') {
    $controller = new ChatController();
    $controller->transfer();
} else {
    echo "404 - Página no encontrada";
}
?>