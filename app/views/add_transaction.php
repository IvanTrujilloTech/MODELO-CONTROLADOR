<?php include 'layout/header.php'; // incluir el encabezado de la pagina ?>

// esta vista muestra el formulario para añadir una nueva transaccion
<main class="container mx-auto py-12 px-4">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Añadir Transacción</h2>
        // formulario que envia los datos al controlador para crear una nueva transaccion
        <form action="/add-transaction" method="post">
            <div class="mb-4">
                <label for="tipo" class="block text-gray-700 text-sm font-bold mb-2">Tipo:</label>
                <select id="tipo" name="tipo" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="ingreso">Ingreso</option>
                    <option value="gasto">Gasto</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="categoria" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                <select id="categoria" name="categoria" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="Salario">Salario</option>
                    <option value="Alquiler">Alquiler</option>
                    <option value="Comida">Comida</option>
                    <option value="Transporte">Transporte</option>
                    <option value="Entretenimiento">Entretenimiento</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="monto" class="block text-gray-700 text-sm font-bold mb-2">Monto (€):</label>
                <input type="number" step="0.01" id="monto" name="monto" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                <input type="text" id="descripcion" name="descripcion" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-6">
                <label for="fecha" class="block text-gray-700 text-sm font-bold mb-2">Fecha:</label>
                <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Añadir Transacción
                </button>
                <a href="/dashboard" class="inline-block align-baseline font-bold text-sm text-emerald-600 hover:text-emerald-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</main>

<?php include 'layout/footer.php'; // incluir el pie de pagina ?>