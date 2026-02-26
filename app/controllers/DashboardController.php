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

    public function __construct() {
        $database = new database();
        $this->db = $database->getconnection();
        $this->movimiento = new movimiento($this->db);
        $this->inversion = new inversion($this->db);
    }

    // Require autenticacion del usuario
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        // Validamos consistencia de sesion
        if ($_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] || 
            $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            Security::log_security_event('session_hijacking_attempt', [
                'user_id' => $_SESSION['user_id'] ?? 'guest',
                'expected_ip' => $_SESSION['ip'],
                'actual_ip' => $_SERVER['REMOTE_ADDR']
            ]);
            session_destroy();
            header("Location: /login");
            exit;
        }

        // Timeout de sesion (30 minutos)
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 1800) {
            Security::log_security_event('session_timeout', ['user_id' => $_SESSION['user_id']]);
            session_destroy();
            header("Location: /login?timeout=1");
            exit;
        }
    }

    // Valida el token CSRF para POST requests
    private function validateCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validate_csrf_token($_POST['csrf_token'] ?? null)) {
                Security::log_security_event('csrf_failure', [
                    'action' => $_POST['action'] ?? 'unknown',
                    'user_id' => $_SESSION['user_id'] ?? 'guest'
                ]);
                return false;
            }
            Security::regenerate_csrf_token();
        }
        return true;
    }

    // Dashboard - muestra balance y transacciones recientes
    public function index() {
        $this->requireAuth();

        date_default_timezone_set('Europe/Madrid');

        $user_id = $_SESSION['user_id'];
        $balance = $this->movimiento->getBalance($user_id);
        $transactions = $this->movimiento->getByUser($user_id)->fetchAll(PDO::FETCH_ASSOC);

        $monthly_income = 0;
        $monthly_expenses = 0;
        $current_month = date('Y-m');
        foreach ($transactions as $t) {
            $transaction_month = date('Y-m', strtotime($t['fecha']));
            if ($transaction_month == $current_month) {
                if ($t['tipo'] == 'ingreso') {
                    $monthly_income += (float)$t['monto'];
                } elseif ($t['tipo'] == 'gasto') {
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
            } else {
                echo "Error al añadir la transacción";
            }
        } else {
            require_once __dir__ . '/../views/add_transaction.php';
        }
    }

    // Muestra las inversiones del usuario
    public function inversiones() {
        $this->requireAuth();

        $user_id = $_SESSION['user_id'];
        $inversiones = $this->inversion->getByUser($user_id)->fetchAll(PDO::FETCH_ASSOC);

        require_once __DIR__ . '/../views/inversiones.php';
    }

    // Compra acciones
    public function comprarAcciones() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                echo "Error de seguridad: Token CSRF inválido.";
                return;
            }

            if (!Security::validate_request_origin()) {
                echo "Error: Petición no válida.";
                return;
            }

            // Validamos campos numericos
            $cantidad = Security::sanitize_float($_POST['cantidad'] ?? 0);
            $precio_compra = Security::sanitize_float($_POST['precio_compra'] ?? 0);

            if ($cantidad <= 0 || $precio_compra <= 0) {
                header("Location: /comprar-acciones?error=invalid_amount");
                exit;
            }

            $total = $cantidad * $precio_compra;

            // Validamos que no pase de los limites
            if ($total > 999999999.99) {
                header("Location: /comprar-acciones?error=amount_too_high");
                exit;
            }

            $user_id = $_SESSION['user_id'];
            $balance = $this->movimiento->getBalance($user_id);

            if ($balance < $total) {
                header("Location: /comprar-acciones?error=insufficient_balance");
                exit;
            }

            // Limpiamos el nombre de la empresa
            $empresa = Security::sanitize_string($_POST['empresa']);
            if (!Security::validate_alpha_numeric($empresa, '/^[a-zA-Z0-9\s.]{1,100}$/')) {
                header("Location: /comprar-acciones?error=invalid_company");
                exit;
            }

            $this->inversion->usuario_id = $user_id;
            $this->inversion->empresa = $empresa;
            $this->inversion->cantidad = $cantidad;
            $this->inversion->precio_compra = $precio_compra;

            if ($this->inversion->create()) {
                // Creamos el registro de transaccion
                $this->movimiento->usuario_id = $user_id;
                $this->movimiento->tipo = 'gasto';
                $this->movimiento->categoria = 'inversiones';
                $this->movimiento->monto = $total;
                $this->movimiento->descripcion = "Compra de acciones de " . $empresa;
                $this->movimiento->fecha = date('Y-m-d');

                if ($this->movimiento->create()) {
                    Security::log_security_event('stock_purchase', [
                        'user_id' => $user_id,
                        'company' => $empresa,
                        'quantity' => $cantidad,
                        'total' => $total
                    ]);
                    header("Location: /inversiones");
                    exit;
                } else {
                    echo "Error al registrar la transacción.";
                }
            } else {
                echo "Error al comprar acciones.";
            }
        } else {
            require_once __DIR__ . '/../views/comprar_acciones.php';
        }
    }

    // Vende acciones
    public function venderAcciones() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                echo "Error de seguridad: Token CSRF inválido.";
                return;
            }

            if (!Security::validate_request_origin()) {
                echo "Error: Petición no válida.";
                return;
            }

            $user_id = $_SESSION['user_id'];
            $inversion_id = Security::sanitize_int($_POST['inversion_id'] ?? 0);
            $cantidad = Security::sanitize_float($_POST['cantidad'] ?? 0);
            $precio_venta = Security::sanitize_float($_POST['precio_venta'] ?? 0);

            if ($inversion_id <= 0 || $cantidad <= 0 || $precio_venta <= 0) {
                header("Location: /inversiones?error=invalid_data");
                exit;
            }

            $total = $cantidad * $precio_venta;

            // Verificamos que sea suya
            $inversiones = $this->inversion->getByUser($user_id)->fetchAll(PDO::FETCH_ASSOC);
            $investment = null;
            foreach ($inversiones as $inv) {
                if ($inv['id'] == $inversion_id) {
                    $investment = $inv;
                    break;
                }
            }

            if (!$investment || $cantidad > $investment['cantidad']) {
                header("Location: /inversiones?error=invalid_sale");
                exit;
            }

            if ($this->inversion->sell($inversion_id, $cantidad)) {
                // Creamos registro de ingreso
                $this->movimiento->usuario_id = $user_id;
                $this->movimiento->tipo = 'ingreso';
                $this->movimiento->categoria = 'inversiones';
                $this->movimiento->monto = $total;
                $this->movimiento->descripcion = "Venta de acciones de " . $investment['empresa'];
                $this->movimiento->fecha = date('Y-m-d');

                if ($this->movimiento->create()) {
                    Security::log_security_event('stock_sale', [
                        'user_id' => $user_id,
                        'company' => $investment['empresa'],
                        'quantity' => $cantidad,
                        'total' => $total
                    ]);
                    header("Location: /inversiones");
                    exit;
                } else {
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

    // Resetea todos los datos del usuario
    public function reset() {
        $this->requireAuth();

        // Validacion CSRF requerida
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->validateCSRF()) {
                echo "Error de seguridad: Token CSRF inválido.";
                return;
            }

            // Comprobamos contrasena para operacion sensible
            if (empty($_POST['confirm_password'])) {
                header("Location: /dashboard?error=password_required");
                exit;
            }

            $user_id = $_SESSION['user_id'];

            // Borramos transacciones
            $query = "DELETE FROM movimientos WHERE usuario_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
            $stmt->execute();

            // Borramos inversiones
            $query = "DELETE FROM inversiones WHERE usuario_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
            $stmt->execute();

            Security::log_security_event('data_reset', ['user_id' => $user_id]);

            header("Location: /dashboard");
            exit;
        } else {
            header("Location: /dashboard");
            exit;
        }
    }
}
