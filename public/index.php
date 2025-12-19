<?php
// Carga básica de rutas manual (para empezar)
require_once '../config/Database.php';
require_once '../app/controllers/HomeController.php';
require_once '../app/controllers/UserController.php';
require_once '../app/controllers/DashboardController.php';

$url = $_SERVER['REQUEST_URI'];

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
} else {
    echo "404 - Página no encontrada";
}
?>