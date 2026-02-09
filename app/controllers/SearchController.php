<?php
// este archivo contiene el controlador para la busqueda semantica
// clase que gestiona la busqueda de transacciones por palabras clave y categorias
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/Movimiento.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';

class SearchController {
    private $db;
    private $movimiento;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->movimiento = new Movimiento($this->db);
    }

    // metodo que busca transacciones por palabras clave
    public function search() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $resultados = [];
        $termino = '';

        if($_GET && isset($_GET['q'])) {
            $termino = sanitizarHTML($_GET['q']);
            $resultados = $this->buscarTransacciones($termino);
        }

        require_once __DIR__ . '/../views/search.php';
    }

    // metodo que busca transacciones relacionadas por categoria
    public function searchByCategory() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $resultados = [];
        $categoria = '';

        if($_GET && isset($_GET['categoria'])) {
            $categoria = sanitizarHTML($_GET['categoria']);
            $resultados = $this->buscarPorCategoria($categoria);
        }

        require_once __DIR__ . '/../views/search_category.php';
    }

    // metodo privado que hace la consulta a la base de datos
    private function buscarTransacciones($termino) {
        $user_id = $_SESSION['user_id'];
        $termino = "%{$termino}%";

        $query = "SELECT * FROM movimientos WHERE usuario_id = ? AND (
            descripcion LIKE ? OR categoria LIKE ? OR tipo LIKE ?
        ) ORDER BY fecha DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $termino);
        $stmt->bindParam(3, $termino);
        $stmt->bindParam(4, $termino);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // metodo privado que busca por categoria
    private function buscarPorCategoria($categoria) {
        $user_id = $_SESSION['user_id'];
        $categoria = "%{$categoria}%";

        $query = "SELECT * FROM movimientos WHERE usuario_id = ? AND categoria LIKE ? ORDER BY fecha DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $categoria);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // metodo que obtiene sugerencias de busqueda
    public function suggest() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if($_GET && isset($_GET['q'])) {
            $termino = sanitizarHTML($_GET['q']);
            $sugerencias = $this->obtenerSugerencias($termino);
            
            header('Content-Type: application/json');
            echo json_encode($sugerencias);
            exit;
        }

        echo json_encode([]);
    }

    // metodo privado que obtiene sugerencias de busqueda
    private function obtenerSugerencias($termino) {
        $user_id = $_SESSION['user_id'];
        $termino = "%{$termino}%";

        $query = "SELECT DISTINCT descripcion, categoria FROM movimientos 
                  WHERE usuario_id = ? AND (descripcion LIKE ? OR categoria LIKE ?) 
                  LIMIT 5";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $termino);
        $stmt->bindParam(3, $termino);
        $stmt->execute();

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $sugerencias = [];

        foreach($resultados as $item) {
            if(!in_array($item['descripcion'], $sugerencias) && !empty($item['descripcion'])) {
                $sugerencias[] = $item['descripcion'];
            }
            if(!in_array($item['categoria'], $sugerencias) && !empty($item['categoria'])) {
                $sugerencias[] = $item['categoria'];
            }
        }

        return $sugerencias;
    }
}
?>