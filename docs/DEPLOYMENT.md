# Guía de Despliegue

Este documento describe el proceso de despliegue de la aplicación de gestión financiera personal en diferentes entornos.

## Requisitos Previos

### 1. Servidor Web

- Apache 2.4+ o Nginx
- PHP 7.4+ (con extensiones: pdo_mysql, mbstring, curl, openssl)
- MySQL 8.0+

### 2. Docker (Opcional)

Si se usa Docker, se necesita:

- Docker Engine 20.0+
- Docker Compose 2.0+

## Despliegue en Servidor Local

### 1. Clonar el Repositorio

```bash
git clone [URL del repositorio]
cd modelo-controlador
```

### 2. Configurar la Base de Datos

1. Crear una base de datos MySQL:
   ```sql
   CREATE DATABASE finanzas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Importar el esquema:
   ```bash
   mysql -u root -p finanzas_db < database.sql
   ```

### 3. Configurar Variables de Entorno

Crear un archivo `.env` en la raíz del proyecto:

```env
# Base de datos
DB_HOST=localhost
DB_NAME=finanzas_db
DB_USER=root
DB_PASS=tu_contraseña

# Webhook n8n
N8N_WEBHOOK_URL=https://n8n.tudominio.com/webhook/tu-webhook-id

# Aplicación
APP_NAME="Gestión Financiera"
APP_ENV=production
APP_DEBUG=false
```

### 4. Configurar el Servidor Web

#### Apache

1. Crear un virtual host:

```apache
<VirtualHost *:80>
    ServerName tu-dominio.com
    DocumentRoot /ruta/al/proyecto/public

    <Directory /ruta/al/proyecto/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/gestion-financiera-error.log
    CustomLog ${APACHE_LOG_DIR}/gestion-financiera-access.log combined
</VirtualHost>
```

2. Habilitar el virtual host:
   ```bash
   a2ensite gestion-financiera.conf
   systemctl restart apache2
   ```

#### Nginx

1. Crear un bloque server:

```nginx
server {
    listen 80;
    server_name tu-dominio.com;
    root /ruta/al/proyecto/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### 5. Configurar Permisos

```bash
chown -R www-data:www-data /ruta/al/proyecto
chmod -R 755 /ruta/al/proyecto
chmod -R 775 /ruta/al/proyecto/logs
chmod 600 /ruta/al/proyecto/.env
```

### 6. Probar la Aplicación

Acceder a la URL de tu dominio en el navegador:
- `/` - Página principal
- `/register` - Registro de usuarios
- `/login` - Inicio de sesión
- `/dashboard` - Dashboard del usuario

## Despliegue con Docker

### 1. Configurar Variables de Entorno

Editar el archivo `docker-compose.yml`:

```yaml
version: '3.8'
services:
  web:
    build: .
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_NAME=finanzas_db
      - DB_USER=root
      - DB_PASS=root
      - N8N_WEBHOOK_URL=https://n8n.tudominio.com/webhook/tu-webhook-id
  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: finanzas_db
      MYSQL_ROOT_PASSWORD: root
    volumes:
      - db_data:/var/lib/mysql
      - ./database.sql:/docker-entrypoint-initdb.d/database.sql
volumes:
  db_data:
```

### 2. Construir y Ejecutar

```bash
docker-compose up -d --build
```

### 3. Acceder a la Aplicación

```
http://localhost:8080
```

## Despliegue en Plesk

### 1. Subir los Archivos

1. Acceder a Plesk
2. Ir a "Archivos" y subir la carpeta del proyecto
3. Extraer el contenido

### 2. Crear la Base de Datos

1. Ir a "Bases de datos"
2. Crear una nueva base de datos
3. Importar el esquema `database.sql`

### 3. Configurar la Aplicación

1. Crear un archivo `.env` con las credenciales de la base de datos
2. Configurar el directorio web para apuntar a la carpeta `public/`

### 4. Configurar PHP

1. Ir a "Configuración PHP"
2. Asegurar que las extensiones necesarias están habilitadas:
   - pdo_mysql
   - mbstring
   - curl
   - openssl

### 5. Testear la Aplicación

Acceder a tu dominio y verificar que la aplicación funcione.

## Despliegue en Heroku

### 1. Crear una Aplicación

1. Acceder a Heroku Dashboard
2. Crear una nueva aplicación
3. Conectar el repositorio GitHub

### 2. Configurar Variables de Entorno

1. Ir a "Settings" → "Config Vars"
2. Añadir las variables:

| Variable | Valor |
|----------|-------|
| DB_HOST | tu-host-mysql |
| DB_NAME | tu-base-de-datos |
| DB_USER | tu-usuario |
| DB_PASS | tu-contraseña |
| N8N_WEBHOOK_URL | https://n8n.tudominio.com/webhook/tu-webhook-id |

### 3. Provisionar Add-ons

1. Añadir ClearDB MySQL
2. Añadir Heroku Redis (opcional para cache)

### 4. Desplegar

1. Ir a "Deploy"
2. Hacer un deploy manual o configurar automatización
3. Esperar que el proceso termine

## Configuración de n8n

### 1. Crear un Flujo de Webhook

1. Abrir n8n
2. Crear un nuevo flujo
3. Añadir un nodo "Webhook"
4. Configurar el método POST
5. Copiar la URL del webhook

### 2. Añadir Nodo de Parseo JSON

1. Añadir un nodo "JSON"
2. Configurar para parsear el cuerpo de la solicitud
3. Añadir un nodo "Set" para estructurar los datos

### 3. Configurar Notificaciones

#### Notificación por Email

1. Añadir un nodo "Email"
2. Configurar con tus credenciales SMTP
3. Crear una plantilla de email:

```html
<h1>Nueva Transacción</h1>
<p>Hola {{ $json['datos']['usuario_nombre'] }}!</p>
<p>Has registrado una nueva transacción:</p>
<ul>
    <li><strong>Tipo:</strong> {{ $json['datos']['transaccion']['tipo'] }}</li>
    <li><strong>Categoría:</strong> {{ $json['datos']['transaccion']['categoria'] }}</li>
    <li><strong>Monto:</strong> €{{ $json['datos']['transaccion']['monto'] }}</li>
    <li><strong>Descripción:</strong> {{ $json['datos']['transaccion']['descripcion'] }}</li>
    <li><strong>Fecha:</strong> {{ $json['datos']['transaccion']['fecha'] }}</li>
</ul>
<p>Atentamente,<br>Tu App de Finanzas</p>
```

#### Notificación por Telegram

1. Añadir un nodo "Telegram"
2. Configurar con tu bot token
3. Añadir el chat ID
4. Crear el mensaje:

```
Nueva Transacción 💰

Hola {{ $json['datos']['usuario_nombre'] }}!

Has registrado una nueva {{ $json['datos']['transaccion']['tipo'] }} de {{ $json['datos']['transaccion']['categoria'] }}:

Monto: €{{ $json['datos']['transaccion']['monto'] }}
Descripción: {{ $json['datos']['transaccion']['descripcion'] }}
Fecha: {{ $json['datos']['transaccion']['fecha'] }}
```

## Pruebas Posteriores al Despliegue

### 1. Pruebas de Funcionalidad

- ✅ Registro de usuarios
- ✅ Inicio de sesión
- ✅ Añadir transacciones
- ✅ Buscar transacciones
- ✅ Visualizar dashboard
- ✅ Verificación de roles
- ✅ Notificaciones webhook

### 2. Pruebas de Seguridad

- ✅ Validación de entrada
- ✅ Sanitización de datos
- ✅ Protección CSRF
- ✅ Prevención SQL injection
- ✅ Control de sesiones
- ✅ Verificación de roles

### 3. Pruebas de Rendimiento

- ✅ Carga de página < 2 segundos
- ✅ Responsividad en dispositivos móviles
- ✅ Carga de imágenes optimizada

## Mantenimiento

### 1. Copias de Seguridad

1. Base de datos:
   ```bash
   mysqldump -u [usuario] -p [base_datos] > backup_$(date +%Y%m%d).sql
   ```

2. Archivos:
   ```bash
   tar -czf backup_$(date +%Y%m%d).tar.gz /ruta/al/proyecto
   ```

### 2. Actualizaciones

1. Clonar la última versión
2. Ejecutar migraciones (si hay)
3. Pruebas en ambiente de staging
4. Desplegar en producción

### 3. Monitoreo

1. Registros de errores: `logs/php_errors.log`
2. Logs de webhook: `logs/webhook_errors.log`
3. Uso de recursos: monitoreo del servidor

## Problemas Comunes

### 1. Error 500 - Internal Server Error

- Verificar permisos de archivos
- Comprobar logs de errores
- Asegurar extensiones PHP están habilitadas

### 2. Error de Conexión a la Base de Datos

- Verificar credenciales en `.env`
- Asegurar el servicio MySQL está corriendo
- Comprobar permisos de usuario

### 3. Notificaciones no Enviadas

- Verificar la URL del webhook en n8n
- Comprobar la conexión a internet
- Verificar los logs del servidor

## Soporte

Si tienes problemas, contacta a tu equipo de desarrollo o revisa:

1. Logs de errores
2. Documentación oficial de PHP
3. Documentación de n8n
4. Foros de soporte

## Conclusión

Este documento proporciona una guía detallada para desplegar la aplicación en diferentes entornos. Asegúrate de seguir las mejores prácticas de seguridad y realizar pruebas exhaustivas antes de poner la aplicación en producción.
