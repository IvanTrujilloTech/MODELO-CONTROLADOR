# Documentacion de Despliegue - Fase 3 (RA5)

## Requisitos del Entorno de Produccion

### Requisitos del Servidor
- php: 8.1 o superior
- mysql: 8.0 o superior / mariadb 10.6+
- extensiones php: pdo, pdo_mysql, openssl, json, mbstring
- servidor web: apache 2.4+ con mod_rewrite o nginx

### Variables de Entorno Requeridas
```env
# base de datos
db_host=localhost
db_name=finanzas_db
db_user=tu_usuario_db
db_pass=tu_contrasena_db_segura

# seguridad
webhook_secret=clave-segura-para-webhooks-minimo-32-caracteres
session_secret=clave-segura-para-sesiones

# n8n integration (opcional)
n8n_webhook_url=https://tu-n8n.com/webhook/post-notification
n8n_user_webhook_url=https://tu-n8n.com/webhook/user-registered
n8n_alert_webhook_url=https://tu-n8n.com/webhook/transaction-alert

# produccion
app_env=production
app_debug=false
```

---

## Despliegue en Plesk

### 1. Preparacion del Proyecto
```bash
# clonar o subir el proyecto al servidor
git clone https://tu-repo/proyecto.git /var/www/vhosts/tu-dominio/httpdocs

# instalar dependencias (si usas composer)
composer install --no-dev --optimize-autoloader
```

### 2. Configuracion de Base de Datos
```bash
# acceder a phpmyadmin o mysql cli
mysql -u usuario -p finanzas_db < database.sql
```

### 3. Configuracion de PHP en Plesk
1. ir a configuracion de php en plesk
2. ajustar:
   - memory_limit: 256m
   - upload_max_filesize: 10m
   - post_max_size: 10m
   - max_execution_time: 60
   - display_errors: desactivado
   - log_errors: activado

### 4. Configuracion de SSL/HTTPS
1. ir a certificados ssl en plesk
2. instalar certificado (lets encrypt o comercial)
3. activar forzar https en la configuracion del dominio

### 5. Permisos de Archivos
```bash
# directorios deben tener permisos de escritura
chmod 755 logs/
chmod 755 tmp/
chmod 644 config/database.php

# proteger archivos sensibles
chmod 600 .env
```

### 6. Configuracion de Cron (opcional)
```bash
# limpieza de sesiones antiguas
0 */4 * * * php /var/www/vhosts/tu-dominio/httpdocs/public/index.php maintenance cleanup
```

---

## Despliegue con Docker

### docker-compose.yml
```yaml
version: '3.8'

services:
  app:
    image: php:8.2-apache
    container_name: blog_app
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    environment:
      - db_host=db
      - db_name=finanzas_db
      - db_user=root
      - db_pass=rootpassword
      - app_env=production
    depends_on:
      - db
    networks:
      - blog_network

  db:
    image: mysql:8.0
    container_name: blog_db
    environment:
      - mysql_root_password=rootpassword
      - mysql_database=finanzas_db
    volumes:
      - mysql_data:/var/lib/mysql
      - ./database.sql:/docker-entrypoint-initdb.d/database.sql
    ports:
      - "3306:3306"
    networks:
      - blog_network

networks:
  blog_network:
    driver: bridge

volumes:
  mysql_data:
```

### Construccion y Ejecucion
```bash
# construir y ejecutar
docker-compose up -d

# ver logs
docker-compose logs -f

# detener
docker-compose down
```

---

## Configuracion de n8n para Webhooks

### 1. Flujo de Notificacion de Nuevos Posts

```
webhook trigger -> set node -> slack/discord/email
```

**configuracion del webhook**:
- url: https://tu-dominio.com/webhook/notify
- metodo: post
- authentication: opcional (verificacion de firma)

**nodos principales**:
1. webhook node: recibe datos del post
2. set node: formatea datos para notificacion
3. slack/discord node: envia mensaje
4. gmail node: envia email con plantilla html

### 2. Plantilla Html para Email
```html
<!doctype html>
<html>
<head>
    <style>
        body { font-family: arial, sans-serif; }
        .header { background: #10b981; color: white; padding: 20px; }
        .content { padding: 20px; }
        .button { background: #10b981; color: white; padding: 10px 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>nuevo post</h1>
    </div>
    <div class="content">
        <h2>{{titulo}}</h2>
        <p>{{resumen}}</p>
        <p><strong>categoria:</strong> {{categoria}}</p>
        <a href="{{url}}" class="button">leer mas</a>
    </div>
</body>
</html>
```

---

## Checklist de Seguridad Post-Despliegue

- https habilitado con certificado valido
- variables de entorno configuradas
- debug mode desactivado (app_debug=false)
- permisos de archivos correctos
- web/config/.htaccess configurado
- logs rotando correctamente
- copia de seguridad programada
- rate limiting activo
- headers de seguridad activos
- base de datos con usuario no-root

---

## Monitoreo y Logs

### Ubicacion de Logs
- php: logs/php_errors.log
- seguridad: logs/security.log
- apache/nginx: configuracion del servidor

### Verificacion de Headers de Seguridad
```bash
curl -i https://tu-dominio.com
```

debe incluir:
```
http/2 200
x-xss-protection: 1; mode=block
x-content-type-options: nosniff
x-frame-options: sameorigin
content-security-policy: ...
strict-transport-security: ...
```
