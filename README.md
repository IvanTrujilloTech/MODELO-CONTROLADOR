# TrujiMoney

## Cómo Navegar por la App (Flujo del Usuario)

La aplicación sigue un camino como muchas otras apps de dinero:

1. **Inicio (Página Principal/Blog)**: Aquí ves una introducción, los beneficios y artículos sobre cómo ahorrar dinero.
2. **Iniciar Sesión/Registrarse**: Para entrar de forma segura.
3. **Panel Principal (Dashboard)**: Un resumen de tu saldo y las últimas cosas que has hecho con tu dinero.
4. **Lista de Transacciones**: Puedes filtrar por tipo (como sueldo, comida, diversión) y por fechas.

## Descripción del Diseño Básico

La página de inicio es simple.

- **Header**: El logo ("TrujiMoney"), enlaces (Blog, Guías), y un botón destacado que dice "Mi Cuenta".
- **Sección Principal**: Un título fuerte como "Toma el control de tu dinero" y una imagen de cómo se ve la app.
- **Lista de Publicaciones**: Tarjetas con consejos sobre finanzas, como "Cómo ahorrar el 20% de tu sueldo".
- **Pie de Página**: Enlaces a cosas legales y redes sociales.

## Sobre la Estructura MVC

- Este HTML simple se forma en páginas PHP en la carpeta `/views`.
- La lista de publicaciones usa algo como un bucle `foreach ($posts as $post)` desde un controlador llamado PostController, que pregunta al modelo Post por los datos.
- El formulario para iniciar sesión envia la info a UserController, que comprueba en la tabla `users` de la base de datos e iniciará tu sesión.