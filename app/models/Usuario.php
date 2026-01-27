<?php
// este archivo define el modelo para los usuarios
// clase que representa un usuario del sistema
class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nombre;
    public $email;
    public $password;

    // constructor que recibe la conexion a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // metodo que crea un nuevo usuario
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nombre=:nombre, email=:email, password=:password";

        $stmt = $this->conn->prepare($query);

        // sanitizar
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // enlazar valores
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);


        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // metodo que verifica si un email ya existe en la base de datos
    public function emailExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        $num = $stmt->rowCount();

        if($num > 0) {
            return true;
        }

        return false;
    }

    // metodo que autentica al usuario verificando la contrasena
    public function login() {
        $query = "SELECT id, nombre, email, password FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($this->password, $row['password'])) {
                $this->id = $row['id'];
                $this->nombre = $row['nombre'];
                $this->email = $row['email'];
                return true;
            }
        }

        return false;
    }

    // metodo que obtiene todos los usuarios ordenados por fecha de creacion
    public function getAllUsers() {
        $query = "SELECT id, nombre, email, created_at FROM " . $this->table_name . " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function getUserById($id) {
        $query = "SELECT id, nombre, email FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nombre=:nombre, email=:email WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":id", $this->id);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function changePassword($newPassword) {
        $query = "UPDATE " . $this->table_name . " SET password=:password WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt->bindParam(":password", $hashed);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
}
?>