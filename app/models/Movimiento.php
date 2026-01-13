<?php
// este archivo define el modelo para los movimientos financieros
// clase que representa un movimiento de dinero
class Movimiento {
    private $conn;
    private $table_name = "movimientos";

    public $id;
    public $usuario_id;
    public $tipo;
    public $categoria;
    public $monto;
    public $descripcion;
    public $fecha;

    // constructor que recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // metodo que obtiene todos los movimientos ordenados por fecha
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // metodo que obtiene movimientos de un usuario ordenados por fecha
    public function getByUser($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE usuario_id = ? ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    // metodo que calcula el balance de un usuario sumando ingresos y restando gastos
    public function getBalance($user_id) {
        $query = "SELECT
            SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) -
            SUM(CASE WHEN tipo = 'gasto' THEN monto ELSE 0 END) AS balance
            FROM " . $this->table_name . " WHERE usuario_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['balance'] ?? 0;
    }

    // metodo que crea un nuevo movimiento en la base de datos
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET usuario_id=:usuario_id, tipo=:tipo, categoria=:categoria, monto=:monto, descripcion=:descripcion, fecha=:fecha";

        $stmt = $this->conn->prepare($query);

        $this->usuario_id = htmlspecialchars(strip_tags($this->usuario_id));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->categoria = htmlspecialchars(strip_tags($this->categoria));
        $this->monto = htmlspecialchars(strip_tags($this->monto));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->fecha = htmlspecialchars(strip_tags($this->fecha));

        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":categoria", $this->categoria);
        $stmt->bindParam(":monto", $this->monto);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":fecha", $this->fecha);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>