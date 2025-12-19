<?php
class HomeController {
    public function index() {
        // Aquí podrías llamar al modelo para traer posts o datos destacados
        require_once __DIR__ . '/../views/home.php';
    }
}
?>