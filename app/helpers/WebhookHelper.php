<?php
// este archivo contiene funciones para enviar notificaciones via webhook
// conecta con n8n para enviar alertas y notificaciones

// funcion que envia datos a un webhook de n8n
function enviarNotificacionWebhook($datos) {
    // url del webhook de n8n (debes configurarla en tu entorno)
    $webhookUrl = getenv('N8N_WEBHOOK_URL') ?: 'https://ivantrubar.app.n8n.cloud/webhook-test/ff373657-1ce7-4512-9329-1b534d87c759';

    // preparar datos para enviar
    $data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'origen' => 'app-finanzas',
        'datos' => $datos
    ];

    // configurar opciones de la solicitud
    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
            'timeout' => 5, // timeout de 5 segundos para evitar retrasos
        ],
    ];

    try {
        $context = stream_context_create($options);
        $resultado = file_get_contents($webhookUrl, false, $context);
        
        if ($resultado === false) {
            // logear error
            logError('Error al enviar notificacion webhook: No se recibio respuesta');
            return false;
        }

        return true;
    } catch (Exception $e) {
        // logear error
        logError('Error al enviar notificacion webhook: ' . $e->getMessage());
        return false;
    }
}

// funcion que envia notificacion de nueva inversión
function enviarNotificacionInversion($inversionData) {
    return enviarNotificacionWebhook([
        'tipo' => 'nueva_inversion',
        'datos' => $inversionData
    ]);
}

// funcion que envia notificacion de venta de acciones
function enviarNotificacionVenta($ventaData) {
    return enviarNotificacionWebhook([
        'tipo' => 'venta_acciones',
        'datos' => $ventaData
    ]);
}

// funcion que logea errores en un archivo
function logError($mensaje) {
    $logFile = __DIR__ . '/../../logs/webhook_errors.log';
    $linea = date('Y-m-d H:i:s') . " - " . $mensaje . "\n";
    file_put_contents($logFile, $linea, FILE_APPEND);
}
?>