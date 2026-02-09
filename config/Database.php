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
        $this->host = getenv('db_host') ?: "localhost";
        $this->db_name = getenv('db_name') ?: "finanzas_db";
        $this->username = getenv('db_user') ?: "root";
        $this->password = getenv('db_pass') ?: "";
    }

    public function getconnection() {
        $this->conn = null;
        try {
            $options = [
                pdo::attr_errmode => pdo::errmode_exception,
                pdo::attr_default_fetch_mode => pdo::fetch_assoc,
                pdo::attr_emulate_prepares => false,
                pdo::mysql_attr_init_command => "set names utf8mb4 collate utf8mb4_unicode_ci"
            ];
            
            $this->conn = new pdo(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                $options
            );
        } catch(pdoexception $exception) {
            error_log("database connection error: " . $exception->getmessage());
            throw new exception("error de conexion a la base de datos");
        }
        return $this->conn;
    }

    // cierra la conexion
    public function closeconnection() {
        $this->conn = null;
    }
}
