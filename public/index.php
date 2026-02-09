<?php
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
} elseif ($url == '/search') {
    require_once '../app/controllers/SearchController.php';
    $controller = new SearchController();
    $controller->search();
} elseif ($url == '/search-by-category') {
    require_once '../app/controllers/SearchController.php';
    $controller = new SearchController();
    $controller->searchByCategory();
} elseif ($url == '/search-suggest') {
    require_once '../app/controllers/SearchController.php';
    $controller = new SearchController();
    $controller->suggest();
} else {
    echo "404 - Página no encontrada";
}
?>