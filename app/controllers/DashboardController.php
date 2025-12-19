<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/Movimiento.php';

class DashboardController {
    private $db;
    private $movimiento;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->movimiento = new Movimiento($this->db);
    }

    public function index() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $balance = $this->movimiento->getBalance($user_id);
        $transactions = $this->movimiento->getByUser($user_id)->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/dashboard.php';
    }

    public function addTransaction() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if($_POST) {
            $this->movimiento->usuario_id = $_SESSION['user_id'];
            $this->movimiento->tipo = $_POST['tipo'];
            $this->movimiento->categoria = $_POST['categoria'];
            $this->movimiento->monto = $_POST['monto'];
            $this->movimiento->descripcion = $_POST['descripcion'];
            $this->movimiento->fecha = $_POST['fecha'];

            if($this->movimiento->create()) {
                header("Location: /dashboard");
                exit;
            } else {
                echo "Error al añadir transacción.";
            }
        } else {
            require_once __DIR__ . '/../views/add_transaction.php';
        }
    }
}
?>