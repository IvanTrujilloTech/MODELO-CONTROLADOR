<?php
// punto de entrada de la aplicacion
// configuracion de seguridad y enrutamiento

// errores en produccion (log en vez de mostrar)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// definimos rutas
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', __DIR__);

// cargamos autoloader
require_once APP_PATH . '/autoload.php';

// cargamos utilidades de seguridad
require_once APP_PATH . '/utils/security.php';

// iniciamos sesion segura
Security::configure_secure_session();

// ponemos headers de seguridad
Security::set_security_headers();

// Carga básica de rutas manual
require_once APP_PATH . '/../config/Database.php';
require_once APP_PATH . '/controllers/HomeController.php';
require_once APP_PATH . '/controllers/UserController.php';
require_once APP_PATH . '/controllers/DashboardController.php';
require_once APP_PATH . '/controllers/ChatController.php';
require_once APP_PATH . '/controllers/PostController.php';
require_once APP_PATH . '/controllers/SearchController.php';
require_once APP_PATH . '/controllers/WebhookController.php';
require_once APP_PATH . '/helpers/SecurityHelper.php';
require_once APP_PATH . '/models/Post.php';

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

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
// Rutas para posts
} elseif ($url == '/posts' || $url == '/posts/') {
    $controller = new PostController();
    $controller->index();
} elseif (preg_match('/^\/posts\/(\d+)$/', $url, $matches)) {
    $controller = new PostController();
    $controller->show($matches[1]);
} elseif ($url == '/posts/create') {
    $controller = new PostController();
    $controller->create();
} elseif ($url == '/posts/store') {
    $controller = new PostController();
    $controller->store();
} elseif (preg_match('/^\/posts\/edit\/(\d+)$/', $url, $matches)) {
    $controller = new PostController();
    $controller->edit($matches[1]);
} elseif (preg_match('/^\/posts\/update\/(\d+)$/', $url, $matches)) {
    $controller = new PostController();
    $controller->update($matches[1]);
} elseif (preg_match('/^\/posts\/delete\/(\d+)$/', $url, $matches)) {
    $controller = new PostController();
    $controller->delete($matches[1]);
} elseif ($url == '/posts/search' || $url == '/posts/search/') {
    $controller = new PostController();
    $controller->search();
} else {
    echo "404 - Página no encontrada";
}
?>