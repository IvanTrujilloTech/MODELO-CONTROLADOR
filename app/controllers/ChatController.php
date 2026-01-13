<?php
// este archivo contiene el controlador para gestionar el chat y las transferencias
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/Transfer.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Movimiento.php';

// clase que maneja las funcionalidades del chat y las transferencias entre usuarios
class ChatController {
    private $db;
    private $message;
    private $transfer;
    private $usuario;
    private $movimiento;

    // constructor que inicializa la conexion a la base de datos y los objetos de los modelos
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->message = new Message($this->db);
        $this->transfer = new Transfer($this->db);
        $this->usuario = new Usuario($this->db);
        $this->movimiento = new Movimiento($this->db);
    }

    // metodo que muestra la pagina del chat verificando la sesion y obteniendo mensajes y usuarios
    public function index() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // Get all messages (for global chat) with user names
        $query = "SELECT m.id, m.sender_id, m.message, m.timestamp, u.nombre as sender_name FROM messages m JOIN usuarios u ON m.sender_id = u.id WHERE m.receiver_id = 0 ORDER BY m.timestamp ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get all users for transfer form
        $users = $this->usuario->getAllUsers()->fetchAll(PDO::FETCH_ASSOC);

        // Get user's balance
        $balance = $this->movimiento->getBalance($user_id);

        require_once __DIR__ . '/../views/chat.php';
    }

    // metodo que procesa las transferencias entre usuarios verificando el saldo y registrando movimientos
    public function transfer() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        if($_POST) {
            $user_id = $_SESSION['user_id'];
            $recipient_id = (int)$_POST['recipient_id'];
            $amount = (float)$_POST['amount'];
            $description = $_POST['description'];

            $balance = $this->movimiento->getBalance($user_id);

            if($balance < $amount) {
                header("Location: /chat?error=insufficient_balance");
                exit;
            }

            $this->transfer->sender_id = $user_id;
            $this->transfer->recipient_id = $recipient_id;
            $this->transfer->amount = $amount;
            $this->transfer->description = $description;

            if($this->transfer->create()) {
                // Deduct from sender
                $this->movimiento->usuario_id = $user_id;
                $this->movimiento->tipo = 'gasto';
                $this->movimiento->categoria = 'transferencias';
                $this->movimiento->monto = $amount;
                $this->movimiento->descripcion = "Transferencia a usuario ID " . $recipient_id . ": " . $description;
                $this->movimiento->fecha = date('Y-m-d');

                if($this->movimiento->create()) {
                    // Add to recipient
                    $this->movimiento->usuario_id = $recipient_id;
                    $this->movimiento->tipo = 'ingreso';
                    $this->movimiento->categoria = 'transferencias';
                    $this->movimiento->monto = $amount;
                    $this->movimiento->descripcion = "Transferencia de usuario ID " . $user_id . ": " . $description;
                    $this->movimiento->fecha = date('Y-m-d');

                    if($this->movimiento->create()) {
                        header("Location: /chat?success=transfer_completed");
                        exit;
                    } else {
                        // Ideally rollback, but for now error
                        echo "Error al registrar ingreso.";
                    }
                } else {
                    echo "Error al registrar gasto.";
                }
            } else {
                echo "Error al crear transferencia.";
            }
        } else {
            header("Location: /chat");
            exit;
        }
    }
}
?>