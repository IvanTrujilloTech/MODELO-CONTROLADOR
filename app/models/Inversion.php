<?php
// este archivo define el modelo para las inversiones
// clase que representa una inversion en acciones
class Inversion {
    private $conn;
    private $table_name = "inversiones";

    public $id;
    public $usuario_id;
    public $empresa;
    public $cantidad;
    public $precio_compra;
    public $fecha_compra;

    // constructor que recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // metodo que obtiene las inversiones de un usuario ordenadas por fecha
    public function getByUser($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE usuario_id = ? ORDER BY fecha_compra DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    // metodo que crea una nueva inversion en la base de datos
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET usuario_id=:usuario_id, empresa=:empresa, cantidad=:cantidad, precio_compra=:precio_compra";

        $stmt = $this->conn->prepare($query);

        $this->usuario_id = htmlspecialchars(strip_tags($this->usuario_id));
        $this->empresa = htmlspecialchars(strip_tags($this->empresa));
        $this->cantidad = htmlspecialchars(strip_tags($this->cantidad));
        $this->precio_compra = htmlspecialchars(strip_tags($this->precio_compra));

        $stmt->bindParam(":usuario_id", $this->usuario_id);
        $stmt->bindParam(":empresa", $this->empresa);
        $stmt->bindParam(":cantidad", $this->cantidad);
        $stmt->bindParam(":precio_compra", $this->precio_compra);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // metodo que vende una cantidad de acciones actualizando o eliminando la inversion
    public function sell($id, $quantity_to_sell) {
        // obtener la inversion actual
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $investment = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$investment) {
            return false;
        }

        $new_quantity = $investment['cantidad'] - $quantity_to_sell;

        if($new_quantity <= 0) {
            // Delete the investment if all shares are sold
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);
            return $stmt->execute();
        } else {
            // Update the quantity
            $query = "UPDATE " . $this->table_name . " SET cantidad = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $new_quantity);
            $stmt->bindParam(2, $id);
            return $stmt->execute();
        }
    }
}
?>