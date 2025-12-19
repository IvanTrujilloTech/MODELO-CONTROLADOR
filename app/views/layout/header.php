<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrujiMoney - Finanzas Personales</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-emerald-600 p-4 text-white shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">TrujiMoney 💸</h1>
            <div>
                <a href="/" class="px-4">Inicio</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="/dashboard" class="px-4">Dashboard</a>
                    <a href="/logout" class="px-4">Salir</a>
                <?php else: ?>
                    <a href="/login" class="px-4">Entrar</a>
                    <a href="/register" class="bg-white text-emerald-600 px-4 py-2 rounded-lg font-bold">Registro</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>