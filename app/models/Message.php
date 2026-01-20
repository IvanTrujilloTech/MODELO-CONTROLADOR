<?php
// este archivo define el modelo para los mensajes
// clase que representa un mensaje en el chat
class Message {
    private $conn;
    private $table_name = "messages";

    public $id;
    public $sender_id;
    public $receiver_id;
    public $message;
    public $timestamp;

    // constructor que recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // metodo que crea un nuevo mensaje
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET sender_id=:sender_id, receiver_id=:receiver_id, message=:message";

        $stmt = $this->conn->prepare($query);

        // sanitizar
        $this->sender_id = htmlspecialchars(strip_tags($this->sender_id));
        $this->receiver_id = $this->receiver_id !== null ? htmlspecialchars(strip_tags($this->receiver_id)) : null;
        $this->message = htmlspecialchars(strip_tags($this->message));

        // enlazar valores
        $stmt->bindParam(":sender_id", $this->sender_id);
        if ($this->receiver_id !== null) {
            $stmt->bindParam(":receiver_id", $this->receiver_id);
        } else {
            $stmt->bindValue(":receiver_id", null, PDO::PARAM_NULL);
        }
        $stmt->bindParam(":message", $this->message);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // metodo que obtiene mensajes entre dos usuarios
    public function getMessagesBetweenUsers($user1, $user2) {
        $query = "SELECT id, sender_id, receiver_id, message, timestamp FROM " . $this->table_name . " WHERE (sender_id = :user1 AND receiver_id = :user2) OR (sender_id = :user2 AND receiver_id = :user1) ORDER BY timestamp ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user1", $user1);
        $stmt->bindParam(":user2", $user2);
        $stmt->execute();

        return $stmt;
    }

    // metodo que obtiene todos los mensajes ordenados por fecha
    public function getAllMessages() {
        $query = "SELECT id, sender_id, receiver_id, message, timestamp FROM " . $this->table_name . " ORDER BY timestamp DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
?>