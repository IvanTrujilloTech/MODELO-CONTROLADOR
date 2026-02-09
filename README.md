# TrujiMoney - Gestión Financiera Personal

## Descripción

TrujiMoney es una aplicación de gestión financiera personal que ayuda a los usuarios a controlar sus ingresos, gastos, inversiones y transacciones. La aplicación incluye características de seguridad avanzada, notificaciones automáticas y búsqueda semántica de transacciones.

## Características Principales

### 1. Gestión de Finanzas
- Registro de ingresos y gastos
- Categorización de transacciones
- Visualización de balance y estadísticas mensuales
- Inversiones en acciones
- Transferencias entre usuarios

### 2. Seguridad
- Validación de entrada de datos
- Sanitización de HTML
- Protección contra CSRF
- Prevención de SQL injection
- Control de sesiones seguras
- Verificación de roles y permisos
- Manejo seguro de contraseñas (bcrypt)

### 3. Búsqueda Semántica
- Búsqueda por descripción, categoría o tipo de transacción
- Sugerencias de búsqueda en tiempo real
- Resultados ordenados por fecha

### 4. Notificaciones
- Webhook para integración con n8n
- Notificaciones por email
- Notificaciones por Telegram
- Logging de errores de webhook

### 5. WebSocket
- Chat en tiempo real entre usuarios
- Transferencias instantáneas

## Estructura MVC

### Modelos
- **Usuario**: Gestiona datos de usuarios
- **Movimiento**: Gestiona transacciones financieras
- **Inversion**: Gestiona inversiones en acciones
- **Message**: Gestiona mensajes del chat
- **Transfer**: Gestiona transferencias entre usuarios

### Controladores
- **HomeController**: Página principal
- **UserController**: Registro, login y gestión de usuarios
- **DashboardController**: Dashboard y transacciones
- **SearchController**: Búsqueda semántica
- **ChatController**: Chat y transferencias
- **WebhookController**: Recibe datos de n8n

### Vistas
- **home.php**: Página principal
- **login.php**: Inicio de sesión
- **register.php**: Registro de usuarios
- **dashboard.php**: Dashboard del usuario
- **add_transaction.php**: Formulario de transacciones
- **inversiones.php**: Gestión de inversiones
- **comprar_acciones.php**: Compra de acciones
- **search.php**: Búsqueda de transacciones
- **chat.php**: Chat en tiempo real
- **users.php**: Lista de usuarios (admin)

## Cómo Navegar por la App (Flujo del Usuario)

1. **Inicio (Página Principal)**: Introducción a la aplicación y beneficios
2. **Iniciar Sesión/Registrarse**: Acceso seguro a la cuenta
3. **Panel Principal (Dashboard)**: Resumen de saldo y transacciones
4. **Búsqueda**: Buscar transacciones por palabras clave
5. **Transacciones**: Añadir, editar y ver transacciones
6. **Inversiones**: Gestionar inversiones en acciones
7. **Chat**: Comunicación con otros usuarios y transferencias

## Requisitos

- PHP 7.4+
- MySQL 8.0+
- Apache 2.4+ o Nginx
- Extensions PHP: pdo_mysql, mbstring, curl, openssl

## Instalación

### Local

1. Clonar el repositorio
2. Configurar base de datos
3. Crear archivo .env
4. Configurar servidor web
5. Probar la aplicación

### Docker

1. Editar docker-compose.yml
2. Construir contenedores: `docker-compose up -d --build`
3. Acceder: http://localhost:8080

## Documentación

- **SECURITY_EVALUATION.md**: Evaluación de seguridad
- **DEPLOYMENT.md**: Guía de despliegue
- **database.sql**: Esquema de la base de datos

## Contribuciones

Las contribuciones son bienvenidas. Por favor, sigue las guías de desarrollo y seguridad.

## Licencia

MIT

