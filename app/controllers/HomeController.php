<?php
// este archivo contiene el controlador para la pagina principal
// clase que gestiona la vista de la pagina de inicio
class HomeController {
    // metodo que carga la vista de la pagina de inicio
    public function index() {
        // aqui podrias llamar al modelo para traer posts o datos destacados
        require_once __DIR__ . '/../views/home.php';
    }
}
?>