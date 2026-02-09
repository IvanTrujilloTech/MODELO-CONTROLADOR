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
    exit;
}

// ejecutamos la accion con parametros
call_user_func_array([$controller, $action], $params);
