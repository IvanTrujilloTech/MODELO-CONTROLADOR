<?php
// Carga básica de rutas manual (para empezar)
require_once '../config/Database.php';
require_once '../app/controllers/HomeController.php';
require_once '../app/controllers/UserController.php';
require_once '../app/controllers/DashboardController.php';
require_once '../app/controllers/ChatController.php';

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

session_start();

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