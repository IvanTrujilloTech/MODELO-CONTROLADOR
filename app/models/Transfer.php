<?php
// este archivo define el modelo para las transferencias
// clase que representa una transferencia de dinero
class Transfer {
    private $conn;
    private $table_name = "transfers";

    public $id;
    public $sender_id;
    public $recipient_id;
    public $amount;
    public $description;
    public $timestamp;

    // constructor que recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // metodo que crea una nueva transferencia
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET sender_id=:sender_id, recipient_id=:recipient_id, amount=:amount, description=:description";

        $stmt = $this->conn->prepare($query);

        // sanitizar
        $this->sender_id = htmlspecialchars(strip_tags($this->sender_id));
        $this->recipient_id = htmlspecialchars(strip_tags($this->recipient_id));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // enlazar valores
        $stmt->bindParam(":sender_id", $this->sender_id);
        $stmt->bindParam(":recipient_id", $this->recipient_id);
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":description", $this->description);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // metodo que obtiene transferencias de un usuario como remitente o destinatario
    public function getTransfersForUser($user_id) {
        $query = "SELECT id, sender_id, recipient_id, amount, description, timestamp FROM " . $this->table_name . " WHERE sender_id = :user_id OR recipient_id = :user_id ORDER BY timestamp DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt;
    }

    // metodo que obtiene todas las transferencias ordenadas por fecha
    public function getAllTransfers() {
        $query = "SELECT id, sender_id, recipient_id, amount, description, timestamp FROM " . $this->table_name . " ORDER BY timestamp DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
?>