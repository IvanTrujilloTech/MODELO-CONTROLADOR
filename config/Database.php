<?php
// configuracion de base de datos
// aqui esta la conexion pdo segura
class database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        $this->host = getenv('DB_HOST') ?: "db";
        $this->db_name = getenv('DB_NAME') ?: "finanzas_db";
        $this->username = getenv('DB_USER') ?: "root";
        $this->password = getenv('DB_PASS') ?: "rootpassword";
    }

    public function getconnection() {
        $this->conn = null;
        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4 collate utf8mb4_unicode_ci"
            ];
            
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                $options
            );
        } catch(PDOException $exception) {
            error_log("database connection error: " . $exception->getMessage());
            throw new Exception("error de conexion a la base de datos");
        }
        return $this->conn;
    }

    // cierra la conexion
    public function closeconnection() {
        $this->conn = null;
    }
}
