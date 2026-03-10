# TrujiMoney

La aplicación sigue un camino como muchas otras apps de dinero:

1. **Inicio (Página Principal/Blog)**: Aquí ves una introducción, los beneficios y artículos sobre cómo ahorrar dinero.
2. **Iniciar Sesión/Registrarse**: Para entrar.
3. **Panel Principal (Dashboard)**: Un resumen de tu saldo y las últimas transacciones que has hecho con tu dinero.
4. **Lista de Transacciones**: Puedes filtrar por tipo (como sueldo, comida, diversión) y por fechas.

## Descripción del Diseño Básico

La página de inicio es simple.

- **Header**: El logo ("TrujiMoney"), enlaces (Blog, Guías), y un botón destacado que dice "Mi Cuenta".
- **Sección Principal**: Un título como "Toma el control de tu dinero" y una imagen de cómo se ve la app.
- **Lista de Publicaciones**: Tarjetas con consejos sobre finanzas, como "Cómo ahorrar el 20% de tu sueldo". (Placeholder)
- **Pie de Página**: Enlaces a cosas legales y redes sociales.

## Sobre la Estructura MVC

- Este HTML simple se forma en páginas PHP en la carpeta `/views`.
- La lista de publicaciones usa un bucle `foreach ($posts as $post)` desde un controlador llamado PostController, que pregunta al modelo Post por los datos.
- El formulario para iniciar sesión envia la info a UserController, que comprueba en la tabla `users` de la base de datos e iniciará tu sesión.

## Capturas de pantalla

<img width="1626" height="837" alt="image" src="https://github.com/user-attachments/assets/31951049-a0e6-4999-b598-852f2201cbe6" />


<img width="1636" height="953" alt="image" src="https://github.com/user-attachments/assets/1165b462-bf2a-445a-8331-353eceebbec4" />

<img width="1595" height="889" alt="image" src="https://github.com/user-attachments/assets/e6272fcf-2af6-4913-af93-5c7b820a340b" />

<img width="1612" height="694" alt="image" src="https://github.com/user-attachments/assets/46c07dbd-54e8-470c-9ac6-7272b46a1c94" />

<img width="1602" height="939" alt="image" src="https://github.com/user-attachments/assets/355787a0-d0bd-49c9-b134-756b90657c0d" />
