<?php
// controlador webhook para integracion con n8n
// aqui se manejan las notificaciones automaticas
class webhookcontroller {
    private $db;
    private $post;
    private $webhooksecret;

    public function __construct() {
        $database = new database();
        $this->db = $database->getconnection();
        $this->post = new post($this->db);
        $this->webhooksecret = getenv('webhook_secret') ?: 'tu-webhook-secret-key-minimo-32-caracteres-cambiar-esto';
    }

    // verifica la firma del webhook
    private function verifysignature() {
        $signature = $_server['http_x_webhook_signature'] ?? '';
        $payload = file_get_contents('php://input');
        
        $expectedsignature = hash_hmac('sha256', $payload, $this->webhooksecret);
        return hash_equals($expectedsignature, $signature);
    }

    // maneja el webhook entrante de n8n
    public function handlewebhook() {
        // comprobamos el metodo
        if ($_server['request_method'] !== 'post') {
            http_response_code(405);
            echo json_encode(['error' => 'method not allowed']);
            return;
        }

        // verificamos la firma si existe
        if (isset($_server['http_x_webhook_signature'])) {
            if (!$this->verifysignature()) {
                security::log_security_event('webhook_invalid_signature', [
                    'ip' => $_server['remote_addr']
                ]);
                http_response_code(401);
                echo json_encode(['error' => 'invalid signature']);
                return;
            }
        }

        // cogemos el json
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'invalid json payload']);
            return;
        }

        // dirigimos segun la accion
        $action = $input['action'] ?? $input['type'] ?? '';

        switch ($action) {
            case 'new_post':
                $this->notifynewpost($input);
                break;
            case 'user_registered':
                $this->notifyuserregistered($input);
                break;
            case 'transaction_alert':
                $this->notifytransactionalert($input);
                break;
            default:
                http_response_code(400);
                echo json_encode(['error' => 'unknown action']);
        }
    }

    // envia notificacion de nuevo post a n8n
    public function notifynewpost($postdata) {
        // validamos que esten los campos obligatorios
        $required = ['titulo', 'resumen', 'categoria'];
        foreach ($required as $field) {
            if (empty($postdata[$field])) {
                http_response_code(400);
                echo json_encode(['error' => "missing required field: {$field}"]);
                return;
            }
        }

        // construimos el payload
        $payload = [
            'event' => 'new_post_published',
            'timestamp' => date('c'),
            'data' => [
                'titulo' => security::sanitize_string($postdata['titulo']),
                'resumen' => security::sanitize_string($postdata['resumen']),
                'categoria' => security::sanitize_string($postdata['categoria']),
                'imagen' => security::sanitize_string($postdata['imagen'] ?? ''),
                'autor_id' => (int)($postdata['autor_id'] ?? 0),
                'url' => security::sanitize_string($postdata['url'] ?? '')
            ],
            'metadata' => [
                'source' => 'blog_system',
                'version' => '1.0'
            ]
        ];

        // mandamos a la url de n8n configurada
        $n8nwebhookurl = getenv('n8n_webhook_url');
        
        if ($n8nwebhookurl) {
            $this->sendtowebhook($n8nwebhookurl, $payload);
        }

        // registramos la notificacion
        security::log_security_event('post_notification_sent', [
            'titulo' => $payload['data']['titulo'],
            'categoria' => $payload['data']['categoria']
        ]);

        echo json_encode(['success' => true, 'message' => 'notification sent']);
    }

    // notifica nuevo registro de usuario
    public function notifyuserregistered($userdata) {
        $payload = [
            'event' => 'new_user_registered',
            'timestamp' => date('c'),
            'data' => [
                'email' => security::sanitize_string($userdata['email'] ?? ''),
                'nombre' => security::sanitize_string($userdata['nombre'] ?? '')
            ]
        ];

        $n8nwebhookurl = getenv('n8n_user_webhook_url');
        if ($n8nwebhookurl) {
            $this->sendtowebhook($n8nwebhookurl, $payload);
        }

        echo json_encode(['success' => true]);
    }

    // notifica alertas de transacciones
    public function notifytransactionalert($transactiondata) {
        $payload = [
            'event' => 'transaction_alert',
            'timestamp' => date('c'),
            'data' => [
                'tipo' => security::sanitize_string($transactiondata['tipo'] ?? ''),
                'monto' => (float)($transactiondata['monto'] ?? 0),
                'categoria' => security::sanitize_string($transactiondata['categoria'] ?? ''),
                'user_id' => (int)($transactiondata['user_id'] ?? 0)
            ]
        ];

        $n8nwebhookurl = getenv('n8n_alert_webhook_url');
        if ($n8nwebhookurl) {
            $this->sendtowebhook($n8nwebhookurl, $payload);
        }

        echo json_encode(['success' => true]);
    }

    // envia datos a una url de webhook
    private function sendtowebhook($url, $data) {
        $ch = curl_init($url);
        
        curl_setopt_array($ch, [
            curlopt_post => true,
            curlopt_postfields => json_encode($data),
            curlopt_httpheader => [
                'content-type: application/json',
                'x-webhook-source: blog-system'
            ],
            curlopt_timeout => 30,
            curlopt_returntransfer => true,
            curlopt_ssl_verifypeer => true,
            curlopt_followlocation => false
        ]);

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, curlinfo_http_code);
        $error = curl_error($ch);
        
        curl_close($ch);

        if ($error) {
            error_log("webhook error: " . $error);
            return false;
        }

        if ($httpcode >= 400) {
            error_log("webhook returned error code: " . $httpcode);
            return false;
        }

        return true;
    }

    // genera plantilla html para email de post
    public static function generateemailtemplate($post) {
        return '
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>nuevo post: ' . htmlspecialchars($post['titulo']) . '</title>
    <style>
        body { font-family: arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .post-title { font-size: 24px; font-weight: bold; color: #111827; margin-bottom: 15px; }
        .post-summary { color: #4b5563; margin-bottom: 20px; }
        .category { display: inline-block; background: #d1fae5; color: #065f46; padding: 5px 12px; border-radius: 20px; font-size: 14px; margin-bottom: 20px; }
        .button { display: inline-block; background: #10b981; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
        .post-image { max-width: 100%; height: auto; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>nuevo post en el blog</h1>
    </div>
    <div class="content">
        <span class="category">' . htmlspecialchars($post['categoria']) . '</span>
        <h2 class="post-title">' . htmlspecialchars($post['titulo']) . '</h2>
        <p class="post-summary">' . htmlspecialchars($post['resumen']) . '</p>
        ' . (!empty($post['imagen']) ? '<img src="' . htmlspecialchars($post['imagen']) . '" alt="imagen del post" class="post-image">' : '') . '
        <div style="text-align: center; margin-top: 30px;">
            <a href="' . htmlspecialchars($post['url'] ?? '#') . '" class="button">leer articulo completo</a>
        </div>
    </div>
    <div class="footer">
        <p>este mensaje fue enviado automaticamente desde el blog.</p>
        <p>© ' . date('y') . ' blog personal. todos los derechos reservados.</p>
    </div>
</body>
</html>';
    }

    // genera plantilla para mensaje de discord
    public static function generatediscordmessage($post) {
        $color = 0x10b981;
        $title = $post['titulo'];
        $description = substr($post['resumen'], 0, 200) . (strlen($post['resumen']) > 200 ? '...' : '');
        
        return [
            'embeds' => [
                [
                    'title' => $title,
                    'description' => $description,
                    'color' => $color,
                    'fields' => [
                        [
                            'name' => 'categoria',
                            'value' => $post['categoria'],
                            'inline' => true
                        ]
                    ],
                    'timestamp' => date('c'),
                    'footer' => [
                        'text' => 'blog personal'
                    ]
                ]
            ],
            'components' => [
                [
                    'type' => 1,
                    'components' => [
                        [
                            'type' => 2,
                            'style' => 5,
                            'label' => 'leer articulo',
                            'url' => $post['url'] ?? '#'
                        ]
                    ]
                ]
            ]
        ];
    }

    // genera plantilla para telegram
    public static function generatetelegrammessage($post) {
        $message = "nuevo post publicado\n\n";
        $message .= "*" . str_replace(['*', '_'], ['\\*', '\\_'], $post['titulo']) . "*\n\n";
        $message .= $post['resumen'] . "\n\n";
        $message .= "categoria: *" . $post['categoria'] . "*\n\n";
        $message .= "[leer articulo completo](" . ($post['url'] ?? '#') . ")";
        
        return $message;
    }
}
