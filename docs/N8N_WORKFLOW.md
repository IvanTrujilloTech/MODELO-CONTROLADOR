# Flujo n8n - Notificaciones de Nuevo Post

## Descripción del Flujo

Este flujo n8n se encarga de:
1. Recibir datos de un nuevo post a través de un webhook
2. Parsear y validar los datos
3. Enviar notificaciones por email, Telegram y Discord
4. Almacenar un registro en una hoja de cálculo Google

## Nodos del Flujo

### 1. Webhook (Nodo 1)
- **Tipo**: Webhook
- **Método**: POST
- **URL**: `https://ivantrubar.app.n8n.cloud/webhook/ff373657-1ce7-4512-9329-1b534d87c759`
- **Función**: Recibe el payload JSON con los datos del nuevo post

### 2. JSON Parse (Nodo 2)
- **Tipo**: JSON
- **Función**: Parsea el cuerpo de la solicitud para extraer los datos del post
- **Configuración**:
  - Operación: Parse String to JSON
  - JSON String: `{{ $json.body }}`

### 3. Set (Nodo 3)
- **Tipo**: Set
- **Función**: Estructura los datos del post para su uso posterior
- **Campos configurados**:
  - titulo: `{{ $json.data.titulo }}`
  - resumen: `{{ $json.data.resumen }}`
  - categoria: `{{ $json.data.categoria }}`
  - imagen: `{{ $json.data.imagen }}`
  - url: `{{ $json.data.url }}`
  - autor_id: `{{ $json.data.autor_id }}`
  - fecha: `{{ $json.timestamp }}`

### 4. Email (Nodo 4)
- **Tipo**: Email
- **Función**: Envía notificación por email con plantilla HTML
- **Configuración**:
  - Credenciales: SMTP personalizadas
  - To: Subscribers list (obtenida de Google Sheets)
  - Subject: `Nuevo post en el Blog Financiero: {{ $json.titulo }}`
  - HTML Body: Plantilla generada por WebhookController
- **Plantilla HTML**:
  ```html
  <!DOCTYPE html>
  <html lang="es">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Nuevo Post</title>
  </head>
  <body>
      <h1>{{ $json.titulo }}</h1>
      <p>{{ $json.resumen }}</p>
      {{#if $json.imagen}}
      <img src="{{ $json.imagen }}" alt="Imagen del post" style="max-width: 100%;">
      {{/if}}
      <p><a href="{{ $json.url }}">Leer artículo completo</a></p>
  </body>
  </html>
  ```

### 5. Telegram (Nodo 5)
- **Tipo**: Telegram
- **Función**: Envía notificación a un canal de Telegram
- **Configuración**:
  - Bot Token: `123456789:ABC-DEF1234ghIkl-zyx57W2v1u123ew11`
  - Chat ID: `-1001234567890`
  - Text: 
    ```
    📢 Nuevo post en el Blog Financiero!
    
    {{ $json.titulo }}
    
    {{ $json.resumen }}
    
    Categoría: {{ $json.categoria }}
    {{#if $json.imagen}}
    🖼️ {{ $json.imagen }}
    {{/if}}
    
    Leer más: {{ $json.url }}
    ```

### 6. Discord (Nodo 6)
- **Tipo**: Discord
- **Función**: Envía notificación a un canal de Discord
- **Configuración**:
  - Bot Token: `your-discord-bot-token`
  - Channel ID: `123456789012345678`
  - Message:
    ```json
    {
      "embeds": [
        {
          "title": "{{ $json.titulo }}",
          "description": "{{ $json.resumen }}",
          "color": 0x10b981,
          "fields": [
            {
              "name": "Categoría",
              "value": "{{ $json.categoria }}",
              "inline": true
            }
          ],
          "image": {{#if $json.imagen}}{"url": "{{ $json.imagen }}"} {{else}}null{{/if}},
          "url": "{{ $json.url }}",
          "timestamp": "{{ $json.fecha }}"
        }
      ]
    }
    ```

### 7. Google Sheets (Nodo 7)
- **Tipo**: Google Sheets
- **Función**: Almacena un registro del post en una hoja de cálculo
- **Configuración**:
  - Credenciales: OAuth 2.0
  - Spreadsheet ID: `1AbC123dEf4Ghi5Jkl6Mno7Pqr8Stu9Vwx0Yza`
  - Range: `Posts!A2`
  - Values:
    - Fecha: `{{ $json.fecha }}`
    - Título: `{{ $json.titulo }}`
    - Categoría: `{{ $json.categoria }}`
    - URL: `{{ $json.url }}`
    - Autor ID: `{{ $json.autor_id }}`

## Pruebas del Flujo

### Prueba 1: Webhook POST
```bash
curl -X POST \
  'https://ivantrubar.app.n8n.cloud/webhook/ff373657-1ce7-4512-9329-1b534d87c759' \
  -H 'Content-Type: application/json' \
  -d '{
    "event": "new_post_published",
    "timestamp": "2024-01-15T10:30:00+01:00",
    "data": {
        "titulo": "Cómo invertir en acciones como principiante",
        "resumen": "Guía completa para empezar a invertir en la bolsa con confianza",
        "categoria": "Inversiones",
        "imagen": "https://example.com/imagen.jpg",
        "autor_id": 1,
        "url": "https://truji-money.com/posts/123"
    },
    "metadata": {
        "source": "blog_system",
        "version": "1.0"
    }
}'
```

### Prueba 2: Verificar Notificaciones
1. **Email**: Asegurar que se recibe la plantilla HTML correcta
2. **Telegram**: Comprobar el formato del mensaje en el canal
3. **Discord**: Verificar el embed con la imagen y el enlace
4. **Google Sheets**: Confirmar que se agrega una nueva fila

## Errores Comunes y Soluciones

1. **Webhook no responde**: Verificar que la URL es correcta y n8n está ejecutándose
2. **Email no enviado**: Verificar credenciales SMTP y configuración DNS
3. **Telegram/Discord fallan**: Asegurar que el bot está en el canal y tiene permisos
4. **Google Sheets error**: Verificar que la hoja existe y el usuario tiene permisos

## Mejoras Futuras

1. **Gestión de suscriptores**: Integrar un formulario de suscripción
2. **Notificaciones personalizadas**: Permitir a los usuarios elegir categorías
3. **Análíticas**: Añadir tracking de clics en las notificaciones
4. **Respaldo**: Guardar posts en un sistema de almacenamiento cloud