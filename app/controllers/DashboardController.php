<?php
// este archivo contiene el controlador para el dashboard que gestiona transacciones e inversiones
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../models/Movimiento.php';
require_once __DIR__ . '/../models/Inversion.php';
require_once __DIR__ . '/../helpers/SecurityHelper.php';

// clase que controla el dashboard del usuario incluyendo transacciones e inversiones
class DashboardController {
    private $db;
    private $movimiento;
    private $inversion;

    // constructor que inicializa la base de datos y los modelos de movimiento e inversion
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->movimiento = new Movimiento($this->db);
        $this->inversion = new Inversion($this->db);
    }

    // metodo que muestra el dashboard con balance ingresos y gastos mensuales
    public function index() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        date_default_timezone_set('Europe/Madrid');

        $user_id = $_SESSION['user_id'];
        $balance = $this->movimiento->getBalance($user_id);
        $transactions = $this->movimiento->getByUser($user_id)->fetchAll(PDO::FETCH_ASSOC);

        $monthly_income = 0;
        $monthly_expenses = 0;
        $current_month = date('Y-m');
        foreach($transactions as $t) {
            $transaction_month = date('Y-m', strtotime($t['fecha']));
            if($transaction_month == $current_month) {
                if($t['tipo'] == 'ingreso') {
                    $monthly_income += (float)$t['monto'];
                } elseif($t['tipo'] == 'gasto') {
                    $monthly_expenses += (float)$t['monto'];
                }
            }
        }

        require_once __DIR__ . '/../views/dashboard.php';
    }

    // metodo que añade una nueva transaccion al sistema
    public function addTransaction() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        date_default_timezone_set('Europe/Madrid');

        if($_POST) {
            // validar token CSRF
            if (!validarCSRFToken($_POST['csrf_token'])) {
                echo "Error de seguridad: Token inválido";
                return;
            }

            // validar campos
            $errores = [];
            if (!validarTipoTransaccion($_POST['tipo'])) {
                $errores[] = "Tipo de transacción inválido";
            }
            if (!validarCategoria($_POST['categoria'])) {
                $errores[] = "Categoría inválida";
            }
            if (!validarMonto($_POST['monto'])) {
                $errores[] = "Monto inválido (debe ser positivo y <= 999999.99)";
            }
            if (!validarFecha($_POST['fecha'])) {
                $errores[] = "Fecha inválida (formato: YYYY-MM-DD)";
            }
            if (empty($_POST['descripcion']) || strlen($_POST['descripcion']) > 200) {
                $errores[] = "Descripción debe tener entre 1 y 200 caracteres";
            }

            if (!empty($errores)) {
                foreach ($errores as $error) {
                    echo $error . "<br>";
                }
                return;
            }

            $this->movimiento->usuario_id = $_SESSION['user_id'];
            $this->movimiento->tipo = sanitizarHTML($_POST['tipo']);
            $this->movimiento->categoria = sanitizarHTML($_POST['categoria']);
            $this->movimiento->monto = (float)$_POST['monto'];
            $this->movimiento->descripcion = sanitizarHTML($_POST['descripcion']);
            $this->movimiento->fecha = $_POST['fecha'];

            if($this->movimiento->create()) {
                // enviar notificacion por webhook
                enviarNotificacionWebhook([
                    'tipo' => 'nueva_transaccion',
                    'usuario_id' => $_SESSION['user_id'],
                    'usuario_nombre' => $_SESSION['user_name'],
                    'transaccion' => [
                        'tipo' => $_POST['tipo'],
                        'categoria' => $_POST['categoria'],
                        'monto' => $_POST['monto'],
                        'descripcion' => $_POST['descripcion'],
                        'fecha' => $_POST['fecha']
                    ]
                ]);

                header("Location: /dashboard");
                exit;
            } else {
                echo "Error al añadir transacción.";
            }
        } else {
            require_once __DIR__ . '/../views/add_transaction.php';
        }
    }

    // metodo que muestra la lista de inversiones del usuario
    public function inversiones() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $inversiones = $this->inversion->getByUser($user_id)->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/inversiones.php';
    }

    // metodo que permite comprar acciones verificando el saldo disponible
    public function comprarAcciones() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        date_default_timezone_set('Europe/Madrid');

        if($_POST) {
            $user_id = $_SESSION['user_id'];
            $cantidad = (float)$_POST['cantidad'];
            $precio_compra = (float)$_POST['precio_compra'];
            $total = $cantidad * $precio_compra;

            $balance = $this->movimiento->getBalance($user_id);

            if($balance < $total) {
                // Insufficient balance, redirect back with error
                header("Location: /comprar-acciones?error=insufficient_balance");
                exit;
            }

            $this->inversion->usuario_id = $user_id;
            $this->inversion->empresa = $_POST['empresa'];
            $this->inversion->cantidad = $cantidad;
            $this->inversion->precio_compra = $precio_compra;

            if($this->inversion->create()) {
                // Create transaction record
                $this->movimiento->usuario_id = $user_id;
                $this->movimiento->tipo = 'gasto';
                $this->movimiento->categoria = 'inversiones';
                $this->movimiento->monto = $total;
                $this->movimiento->descripcion = "Compra de acciones de " . $_POST['empresa'];
                $this->movimiento->fecha = date('Y-m-d');

                if($this->movimiento->create()) {
                    header("Location: /inversiones");
                    exit;
                } else {
                    // Transaction failed, but investment created - ideally rollback, but for now error
                    echo "Error al registrar la transacción.";
                }
            } else {
                echo "Error al comprar acciones.";
            }
        } else {
            require_once __DIR__ . '/../views/comprar_acciones.php';
        }
    }

    // metodo que permite vender acciones verificando la propiedad y cantidad
    public function venderAcciones() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        date_default_timezone_set('Europe/Madrid');

        if($_POST) {
            $user_id = $_SESSION['user_id'];
            $inversion_id = (int)$_POST['inversion_id'];
            $cantidad = (float)$_POST['cantidad'];
            $precio_venta = (float)$_POST['precio_venta'];
            $total = $cantidad * $precio_venta;

            // Verify the investment belongs to the user
            $inversiones = $this->inversion->getByUser($user_id)->fetchAll(PDO::FETCH_ASSOC);
            $investment = null;
            foreach($inversiones as $inv) {
                if($inv['id'] == $inversion_id) {
                    $investment = $inv;
                    break;
                }
            }

            if(!$investment || $cantidad > $investment['cantidad']) {
                // Invalid investment or quantity
                header("Location: /inversiones?error=invalid_sale");
                exit;
            }

            if($this->inversion->sell($inversion_id, $cantidad)) {
                // Create transaction record for income
                $this->movimiento->usuario_id = $user_id;
                $this->movimiento->tipo = 'ingreso';
                $this->movimiento->categoria = 'inversiones';
                $this->movimiento->monto = $total;
                $this->movimiento->descripcion = "Venta de acciones de " . $investment['empresa'];
                $this->movimiento->fecha = date('Y-m-d');

                if($this->movimiento->create()) {
                    header("Location: /inversiones");
                    exit;
                } else {
                    // Transaction failed, but investment updated - ideally rollback, but for now error
                    echo "Error al registrar la transacción.";
                }
            } else {
                echo "Error al vender acciones.";
            }
        } else {
            header("Location: /inversiones");
            exit;
        }
    }

    // metodo que elimina todas las transacciones e inversiones del usuario
    public function reset() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // Delete all transactions for the user
        $query = "DELETE FROM movimientos WHERE usuario_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();

        // Delete all investments for the user
        $query = "DELETE FROM inversiones WHERE usuario_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();

        // Redirect back to dashboard
        header("Location: /dashboard");
        exit;
    }
}
?>