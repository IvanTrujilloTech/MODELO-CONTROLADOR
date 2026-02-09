<?php
// controlador del dashboard
// aqui se gestionan las transacciones e inversiones con seguridad
class dashboardcontroller {
    private $db;
    private $movimiento;
    private $inversion;

    public function __construct() {
        $database = new database();
        $this->db = $database->getconnection();
        $this->movimiento = new movimiento($this->db);
        $this->inversion = new inversion($this->db);
    }

    //require autenticacion del usuario
    private function requireauth() {
        if (!isset($_session['user_id'])) {
            header("location: /login");
            exit;
        }

        // validamos consistencia de sesion
        if ($_session['ip'] !== $_server['remote_addr'] || 
            $_session['user_agent'] !== $_server['http_user_agent']) {
            security::log_security_event('session_hijacking_attempt', [
                'user_id' => $_session['user_id'] ?? 'guest',
                'expected_ip' => $_session['ip'],
                'actual_ip' => $_server['remote_addr']
            ]);
            session_destroy();
            header("location: /login");
            exit;
        }

        // timeout de sesion (30 minutos)
        if (isset($_session['login_time']) && (time() - $_session['login_time']) > 1800) {
            security::log_security_event('session_timeout', ['user_id' => $_session['user_id']]);
            session_destroy();
            header("location: /login?timeout=1");
            exit;
        }
    }

    // valida el token csrf para post requests
    private function validatecsrf() {
        if ($_server['request_method'] === 'post') {
            if (!security::validate_csrf_token($_post['csrf_token'] ?? null)) {
                security::log_security_event('csrf_failure', [
                    'action' => $_post['action'] ?? 'unknown',
                    'user_id' => $_session['user_id'] ?? 'guest'
                ]);
                return false;
            }
            security::regenerate_csrf_token();
        }
        return true;
    }

    // dashboard - muestra balance y transacciones recientes
    public function index() {
        $this->requireauth();

        date_default_timezone_set('europe/madrid');

        $user_id = $_session['user_id'];
        $balance = $this->movimiento->getbalance($user_id);
        $transactions = $this->movimiento->getbyuser($user_id)->fetchall(pdo::fetch_assoc);

        $monthly_income = 0;
        $monthly_expenses = 0;
        $current_month = date('y-m');
        foreach ($transactions as $t) {
            $transaction_month = date('y-m', strtotime($t['fecha']));
            if ($transaction_month == $current_month) {
                if ($t['tipo'] == 'ingreso') {
                    $monthly_income += (float)$t['monto'];
                } elseif ($t['tipo'] == 'gasto') {
                    $monthly_expenses += (float)$t['monto'];
                }
            }
        }

        require_once __dir__ . '/../views/dashboard.php';
    }

    // anade una nueva transaccion
    public function addtransaction() {
        $this->requireauth();

        if ($_server['request_method'] === 'post') {
            if (!$this->validatecsrf()) {
                echo "error de seguridad: token csrf invalido.";
                return;
            }

            if (!security::validate_request_origin()) {
                security::log_security_event('origin_failure', ['action' => 'add_transaction']);
                echo "error: peticion no valida.";
                return;
            }

            // comprovamos que esten los campos obligatorios
            $required_fields = ['tipo', 'categoria', 'monto', 'fecha'];
            foreach ($required_fields as $field) {
                if (empty($_post[$field])) {
                    echo "error: el campo {$field} es obligatorio.";
                    return;
                }
            }

            // validamos el tipo de transaccion
            $tipo = security::sanitize_string($_post['tipo']);
            if (!in_array($tipo, ['ingreso', 'gasto'])) {
                echo "error: tipo de transaccion invalido.";
                return;
            }

            // validamos el monto
            $monto = security::sanitize_float($_post['monto']);
            if ($monto <= 0 || $monto > 999999999.99) {
                echo "error: el monto debe ser mayor a 0.";
                return;
            }

            // validamos el formato de fecha
            $fecha = $_post['fecha'];
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
                echo "error: formato de fecha invalido.";
                return;
            }

            // limpiamos la descripcion si la hay
            $descripcion = security::sanitize_string($_post['descripcion'] ?? '');

            // validamos la categoria
            $categoria = security::sanitize_string($_post['categoria']);
            if (!security::validate_alpha_numeric($categoria, '/^[a-za-z0-9\saeiou]{1,50}$/')) {
                echo "error: categoria con caracteres no validos.";
                return;
            }

            // metemos los datos en el movimiento
            $this->movimiento->usuario_id = $_session['user_id'];
            $this->movimiento->tipo = $tipo;
            $this->movimiento->categoria = $categoria;
            $this->movimiento->monto = $monto;
            $this->movimiento->descripcion = $descripcion;
            $this->movimiento->fecha = $fecha;

            if ($this->movimiento->create()) {
                security::log_security_event('transaction_created', [
                    'user_id' => $_session['user_id'],
                    'type' => $tipo,
                    'amount' => $monto
                ]);
                header("location: /dashboard");
                exit;
            } else {
                echo "error al anadir transaccion.";
            }
        } else {
            require_once __dir__ . '/../views/add_transaction.php';
        }
    }

    // muestra las inversiones del usuario
    public function inversiones() {
        $this->requireauth();

        $user_id = $_session['user_id'];
        $inversiones = $this->inversion->getbyuser($user_id)->fetchall(pdo::fetch_assoc);

        require_once __dir__ . '/../views/inversiones.php';
    }

    // compra acciones
    public function compraracciones() {
        $this->requireauth();

        if ($_server['request_method'] === 'post') {
            if (!$this->validatecsrf()) {
                echo "error de seguridad: token csrf invalido.";
                return;
            }

            if (!security::validate_request_origin()) {
                echo "error: peticion no valida.";
                return;
            }

            // validamos campos numericos
            $cantidad = security::sanitize_float($_post['cantidad'] ?? 0);
            $precio_compra = security::sanitize_float($_post['precio_compra'] ?? 0);

            if ($cantidad <= 0 || $precio_compra <= 0) {
                header("location: /comprar-acciones?error=invalid_amount");
                exit;
            }

            $total = $cantidad * $precio_compra;

            // validamos que no pase de los limites
            if ($total > 999999999.99) {
                header("location: /comprar-acciones?error=amount_too_high");
                exit;
            }

            $user_id = $_session['user_id'];
            $balance = $this->movimiento->getbalance($user_id);

            if ($balance < $total) {
                header("location: /comprar-acciones?error=insufficient_balance");
                exit;
            }

            // limpiamos el nombre de la empresa
            $empresa = security::sanitize_string($_post['empresa']);
            if (!security::validate_alpha_numeric($empresa, '/^[a-za-z0-9\s.]{1,100}$/')) {
                header("location: /comprar-acciones?error=invalid_company");
                exit;
            }

            $this->inversion->usuario_id = $user_id;
            $this->inversion->empresa = $empresa;
            $this->inversion->cantidad = $cantidad;
            $this->inversion->precio_compra = $precio_compra;

            if ($this->inversion->create()) {
                // creamos el registro de transaccion
                $this->movimiento->usuario_id = $user_id;
                $this->movimiento->tipo = 'gasto';
                $this->movimiento->categoria = 'inversiones';
                $this->movimiento->monto = $total;
                $this->movimiento->descripcion = "compra de acciones de " . $empresa;
                $this->movimiento->fecha = date('y-m-d');

                if ($this->movimiento->create()) {
                    security::log_security_event('stock_purchase', [
                        'user_id' => $user_id,
                        'company' => $empresa,
                        'quantity' => $cantidad,
                        'total' => $total
                    ]);
                    header("location: /inversiones");
                    exit;
                } else {
                    echo "error al registrar la transaccion.";
                }
            } else {
                echo "error al comprar acciones.";
            }
        } else {
            require_once __dir__ . '/../views/comprar_acciones.php';
        }
    }

    // vende acciones
    public function venderacciones() {
        $this->requireauth();

        if ($_server['request_method'] === 'post') {
            if (!$this->validatecsrf()) {
                echo "error de seguridad: token csrf invalido.";
                return;
            }

            if (!security::validate_request_origin()) {
                echo "error: peticion no valida.";
                return;
            }

            $user_id = $_session['user_id'];
            $inversion_id = security::sanitize_int($_post['inversion_id'] ?? 0);
            $cantidad = security::sanitize_float($_post['cantidad'] ?? 0);
            $precio_venta = security::sanitize_float($_post['precio_venta'] ?? 0);

            if ($inversion_id <= 0 || $cantidad <= 0 || $precio_venta <= 0) {
                header("location: /inversiones?error=invalid_data");
                exit;
            }

            $total = $cantidad * $precio_venta;

            // verificamos que sea suya
            $inversiones = $this->inversion->getbyuser($user_id)->fetchall(pdo::fetch_assoc);
            $investment = null;
            foreach ($inversiones as $inv) {
                if ($inv['id'] == $inversion_id) {
                    $investment = $inv;
                    break;
                }
            }

            if (!$investment || $cantidad > $investment['cantidad']) {
                header("location: /inversiones?error=invalid_sale");
                exit;
            }

            if ($this->inversion->sell($inversion_id, $cantidad)) {
                // creamos registro de ingreso
                $this->movimiento->usuario_id = $user_id;
                $this->movimiento->tipo = 'ingreso';
                $this->movimiento->categoria = 'inversiones';
                $this->movimiento->monto = $total;
                $this->movimiento->descripcion = "venta de acciones de " . $investment['empresa'];
                $this->movimiento->fecha = date('y-m-d');

                if ($this->movimiento->create()) {
                    security::log_security_event('stock_sale', [
                        'user_id' => $user_id,
                        'company' => $investment['empresa'],
                        'quantity' => $cantidad,
                        'total' => $total
                    ]);
                    header("location: /inversiones");
                    exit;
                } else {
                    echo "error al registrar la transaccion.";
                }
            } else {
                echo "error al vender acciones.";
            }
        } else {
            header("location: /inversiones");
            exit;
        }
    }

    // resetea todos los datos del usuario
    public function reset() {
        $this->requireauth();

        // validacion csrf requerida
        if ($_server['request_method'] === 'post') {
            if (!$this->validatecsrf()) {
                echo "error de seguridad: token csrf invalido.";
                return;
            }

            // comprovamos contrasena para operacion sensible
            if (empty($_post['confirm_password'])) {
                header("location: /dashboard?error=password_required");
                exit;
            }

            $user_id = $_session['user_id'];

            // borramos transacciones
            $query = "delete from movimientos where usuario_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindparam(1, $user_id, pdo::param_int);
            $stmt->execute();

            // borramos inversiones
            $query = "delete from inversiones where usuario_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindparam(1, $user_id, pdo::param_int);
            $stmt->execute();

            security::log_security_event('data_reset', ['user_id' => $user_id]);

            header("location: /dashboard");
            exit;
        } else {
            header("location: /dashboard");
            exit;
        }
    }
}
