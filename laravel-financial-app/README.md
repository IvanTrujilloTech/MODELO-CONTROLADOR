# Finanzas Personales - Aplicación de Gestión Financiera Personal

## Descripción

Finanzas Personales es una aplicación web de gestión financiera personal desarrollada con Laravel 10 que permite a los usuarios:

- Gestionar ingresos y gastos
- Trackear transacciones financieras
- Invertir en acciones con cotizaciones en tiempo real
- Transferir dinero entre usuarios
- Chat en tiempo real con otros usuarios
- Consultar artículos financieros en el blog
- Recibir notificaciones de actividades importantes
- Ver métricas y resúmenes financieros en un dashboard

## Tecnologías Utilizadas

- **Laravel 10** - Framework PHP
- **Jetstream** - Sistema de autenticación y scaffolding
- **Livewire** - Componentes dinámicos sin JavaScript
- **Tailwind CSS** - Framework CSS utilitario
- **MySQL** - Base de datos relacional
- **Pusher** - WebSocket para funcionalidades en tiempo real

## Instalación

### Requisitos Previos

- PHP 8.1 o superior
- Composer
- Node.js y npm
- MySQL 5.7 o superior

### Pasos de Instalación

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/tu-usuario/finanzas-personales.git
   cd finanzas-personales
   ```

2. **Instalar dependencias PHP:**
   ```bash
   composer install
   ```

3. **Instalar dependencias Node.js:**
   ```bash
   npm install
   ```

4. **Copiar archivo de configuración:**
   ```bash
   cp .env.example .env
   ```

5. **Generar clave de aplicación:**
   ```bash
   php artisan key:generate
   ```

6. **Configurar base de datos:**
   Editar el archivo `.env` y configurar las credenciales de la base de datos:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=finanzas_db
   DB_USERNAME=tu-usuario
   DB_PASSWORD=tu-contraseña
   ```

7. **Ejecutar migraciones y seeders:**
   ```bash
   php artisan migrate --seed
   ```

8. **Compilar assets:**
   ```bash
   npm run dev
   ```

9. **Iniciar servidor de desarrollo:**
   ```bash
   php artisan serve
   ```

## Acceso a la Aplicación

- **URL de desarrollo:** http://localhost:8000
- **Cuenta de prueba (Admin):**
  - Email: admin@example.com
  - Password: password
- **Cuenta de prueba (User):**
  - Email: demo@example.com
  - Password: password

## Funcionalidades Principales

### Dashboard
- Resumen financiero con saldo, ingresos y gastos
- Estadísticas mensuales
- Últimas transacciones
- Botón para resetear datos de prueba

### Transacciones
- Añadir nuevas transacciones (ingresos o gastos)
- Categorizar transacciones (salario, alquiler, comida, etc.)
- Mostrar histórico de transacciones
- Calcular balance automáticamente

### Inversiones
- Comprar acciones con cotizaciones actualizadas
- Vender acciones existentes
- Trackear valor de inversiones
- Calcular beneficios/pérdidas

### Transferencias
- Transferir dinero entre usuarios
- Ver histórico de transferencias
- Establecer límites de seguridad
- Verificar destinatarios antes de transferir

### Chat
- Chat en tiempo real con otros usuarios
- Notificaciones de nuevos mensajes
- Historial de conversaciones
- Búsqueda de usuarios

### Blog
- Artículos sobre finanzas y economía
- Comentarios en artículos
- Categorización de posts
- Búsqueda de contenido

### Notificaciones
- Alertas de actividades importantes
- Notificaciones de transacciones
- Recordatorios de inversiones
- Mensajes de otros usuarios

## Estructura del Proyecto

```
laravel-financial-app/
├── app/
│   ├── Models/
│   │   ├── Comment.php
│   │   ├── Notification.php
│   │   ├── Post.php
│   │   ├── Transaction.php
│   │   ├── Transfer.php
│   │   ├── Investment.php
│   │   ├── Message.php
│   │   └── User.php
│   ├── Http/
│   │   └── Controllers/
│   │       ├── HomeController.php
│   │       ├── DashboardController.php
│   │       ├── TransactionController.php
│   │       ├── InvestmentController.php
│   │       ├── TransferController.php
│   │       ├── ChatController.php
│   │       ├── PostController.php
│   │       ├── CommentController.php
│   │       └── NotificationController.php
│   └── ...
├── database/
│   ├── migrations/
│   │   └── *.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   ├── views/
│   │   ├── layout/
│   │   │   ├── header.blade.php
│   │   │   ├── footer.blade.php
│   │   │   ├── notification-scripts.blade.php
│   │   │   └── app.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── notifications.blade.php
│   │   ├── add_transaction.blade.php
│   │   ├── investments.blade.php
│   │   ├── comprar_acciones.blade.php
│   │   ├── transfers.blade.php
│   │   ├── create_transfer.blade.php
│   │   ├── chat.blade.php
│   │   ├── posts.blade.php
│   │   ├── post_detail.blade.php
│   │   ├── create_post.blade.php
│   │   ├── edit_post.blade.php
│   │   └── search_posts.blade.php
│   └── ...
├── routes/
│   ├── web.php
│   └── api.php
├── .env
├── .env.example
├── composer.json
├── package.json
└── ...
```

## Migraciones y Seeders

### Migraciones
El proyecto incluye migraciones para las siguientes tablas:
- `users` - Usuarios
- `transactions` - Transacciones
- `investments` - Inversiones
- `transfers` - Transferencias
- `messages` - Mensajes
- `posts` - Posts del blog
- `comments` - Comentarios
- `notifications` - Notificaciones

### Seeders
El seeder principal (`DatabaseSeeder.php`) inserta:
- Usuarios de prueba (admin y demo)
- Artículos de blog de ejemplo
- Datos de prueba para demostración

## Contribuciones

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Realiza tus cambios y commits
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

## Licencia

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
