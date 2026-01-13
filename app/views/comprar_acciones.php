<?php include 'layout/header.php'; ?>

<main class="container mx-auto py-12 px-4">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Comprar Acciones</h2>
        <?php if(isset($_GET['error']) && $_GET['error'] == 'insufficient_balance'): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                Saldo insuficiente para realizar la compra.
            </div>
        <?php endif; ?>
        <form action="/comprar-acciones" method="post">
            <div class="mb-4">
                <label for="empresa" class="block text-gray-700 text-sm font-bold mb-2">Empresa:</label>
                <input type="text" id="empresa" name="empresa" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ej: Apple Inc.">
            </div>
            <div class="mb-4">
                <label for="cantidad" class="block text-gray-700 text-sm font-bold mb-2">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="1">
            </div>
            <div class="mb-6">
                <label for="precio_compra" class="block text-gray-700 text-sm font-bold mb-2">Precio Compra (€):</label>
                <input type="number" step="0.01" id="precio_compra" name="precio_compra" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Comprar Acciones
                </button>
                <a href="/inversiones" class="inline-block align-baseline font-bold text-sm text-emerald-600 hover:text-emerald-800">
                    Cancelar
                </a>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Vender Acciones
                </button>
            </div>
        </form>
    </div>
</main>

<?php include 'layout/footer.php'; ?>