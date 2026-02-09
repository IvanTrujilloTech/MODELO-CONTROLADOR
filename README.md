# TrujiMoney - Sistema de Gestion Financiera Personal

## Fase 3 (RA5): Integracion, Automatizacion y Seguridad Avanzada

Este proyecto corresponde a la fase 3 del desarrollo, que integra servicios externos, automatizacion con n8n, funcionalidades rag y seguridad avanzada.

---

## Caracteristicas de la Fase 3

### 1. Integracion con Servicios Externos
- webhook para n8n: notificaciones automaticas al publicar nuevos posts
- plantillas html: para emails, discord y telegram
- firma de webhooks: verificacion de autenticidad con hmac-sha256

### 2. Funcionalidad Rag (Retrieval-Augmented Generation)
- busqueda semantica: posts relacionados basados en palabras clave
- extraccion de keywords: algoritmo nlp basico para extraer terminos importantes
- puntuacion de relevancia: sistema de ranking por coincidencia

### 3. Seguridad Avanzada
- proteccion csrf con tokens
- rate limiting (5 intentos, 15 min bloque)
- sesiones seguras (httponly, samesite, secure)
- hash de contrasenas con argon2id
- headers de seguridad http
- validacion de entrada y sanitizacion
- prepared statements contra sql injection

---

## Estructura del Proyecto

```
modelo-controlador/
├── app/
│   ├── controllers/
│   │   ├── usercontroller.php      // gestion de usuarios (login/register)
│   │   ├── dashboardcontroller.php // transacciones e inversiones
│   │   ├── webhookcontroller.php   // integracion n8n
│   │   └── homecontroller.php      // pagina principal
│   ├── models/
│   │   ├── usuario.php             // modelo de usuarios
│   │   ├── post.php                // modelo con rag
│   │   ├── movimiento.php          // transacciones financieras
│   │   ├── inversion.php           // inversiones en bolsa
│   │   └── message.php             // mensajes de chat
│   ├── utils/
│   │   └── security.php            // utilidades de seguridad
│   └── views/
│       ├── layout/
│       ├── register.php            // formulario registro
│       ├── login.php               // formulario login
│       ├── dashboard.php           // panel principal
│       └── ...
├── config/
│   └── database.php                // conexion pdo segura
├── public/
│   ├── index.php                   // entry point seguro
│   └── .htaccess                  // configuracion apache
├── logs/
│   ├── .gitkeep
│   ├── php_errors.log
│   └── security.log
├── docs/
│   ├── deployment.md               // guia de despliegue
│   └── security_evaluation.md      // evaluacion de seguridad
└── database.sql                    // esquema de base de datos
```

---

## Medidas de Seguridad Implementadas

| medida | descripcion | archivo |
|--------|-------------|---------|
| csrf token | tokens de 32 bytes con timing-safe compare | security.php |
| rate limiting | 5 intentos max, 15 min bloqueo | security.php |
| secure sessions | httponly + samesite + secure | security.php |
| password hashing | argon2id algorithm | security.php |
| input sanitization | htmlspecialchars + validation | security.php |
| prepared statements | pdo con bindparam | todos los modelos |
| security headers | csp, x-frame, xss-protection | .htaccess |
| error handling | logs sin exposicion | index.php |

---

## Configuracion para Produccion

### Variables de Entorno
```
db_host=localhost
db_name=finanzas_db
db_user=root
db_pass=contrasena_segura
webhook_secret=clave-muy-segura-minimo-32-caracteres
n8n_webhook_url=https://tu-n8n.com/webhook/post
app_env=production
```

### Permisos
```
chmod 600 .env
chmod 755 logs/
chmod 644 config/database.php
```

---

## Integracion n8n

### Webhook Endpoint
```
post /webhook/notify
content-type: application/json

{
  "action": "new_post",
  "titulo": "titulo del post",
  "resumen": "resumen del contenido",
  "categoria": "finanzas",
  "imagen": "https://...",
  "url": "https://..."
}
```

### Flujo n8n Recomendado
1. webhook node: recibe datos del post
2. switch node: dirige segun tipo (email, discord, telegram)
3. gmail/smtp: envia email con plantilla html
4. discord/slack: envia notificacion
5. set node: formatea mensaje para cada plataforma

---

## Busqueda Rag (Posts Relacionados)

```php
// ejemplo de uso
$post = new post($db);
$post->getbyid(1);

// extraer keywords del contenido
$keywords = $post->extractkeywords($contenido, 5);

// buscar posts relacionados
$relacionados = $post->findrelatedposts(1, $keywords, 3);
```

---

## Pruebas de Seguridad

```bash
// verificar headers de seguridad
curl -i https://tu-dominio.com

// probar csrf
curl -x post -d "email=test&password=test" https://tu-dominio.com/login

// probar rate limiting
for i in {1..10}; do curl -x post ...; done
```

---

## Documentacion

- guia de despliegue: docs/deployment.md
- evaluacion de seguridad: docs/security_evaluation.md
- api webhook: app/controllers/webhookcontroller.php

---

## Autor
- ivan trubar - desarrollo completo

## Licencia
este proyecto es parte de un trabajo academico para el ciclo de desarrollo de aplicaciones web.
