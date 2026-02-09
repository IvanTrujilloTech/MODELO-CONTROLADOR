<?php include 'layout/header.php'; ?>

// esta vista muestra los resultados de la busqueda de transacciones
<main class="container mx-auto py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl font-bold mb-8 text-gray-800">Buscar Transacciones</h2>
        
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6 mb-8">
            <form action="/search" method="get">
                <div class="flex items-center">
                    <input type="text" name="q" 
                           value="<?php echo escaparHTML($termino ?? ''); ?>"
                           placeholder="Buscar por descripción, categoría o tipo..."
                           class="shadow appearance-none border rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-emerald-500"
                           required>
                    <button type="submit" class="ml-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                        Buscar
                    </button>
                </div>
            </form>
        </div>

        <?php if(!empty($resultados)): ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-700">Resultados de la Búsqueda</h3>
                    <p class="text-sm text-gray-500 mt-1">Encontrados <?php echo count($resultados); ?> resultados</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach($resultados as $transaction): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo escaparHTML($transaction['fecha']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $transaction['tipo'] == 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo escaparHTML(ucfirst($transaction['tipo'])); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo escaparHTML($transaction['categoria']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900"><?php echo escaparHTML($transaction['descripcion']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium <?php echo $transaction['tipo'] == 'ingreso' ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo $transaction['tipo'] == 'ingreso' ? '+' : '-'; ?>€<?php echo number_format($transaction['monto'], 2); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif(isset($termino) && !empty($termino)): ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-8 text-center">
                <p class="text-gray-500 text-lg">No se encontraron transacciones que coincidan con "<?php echo escaparHTML($termino); ?>"</p>
                <p class="text-gray-400 mt-2">Intenta con otras palabras clave</p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-8 text-center">
                <p class="text-gray-500 text-lg">Ingresa una palabra clave para buscar transacciones</p>
                <p class="text-gray-400 mt-2">Busca por descripción, categoría o tipo de transacción</p>
            </div>
        <?php endif; ?>

        <div class="mt-8 text-center">
            <a href="/dashboard" class="inline-block align-baseline font-bold text-sm text-emerald-600 hover:text-emerald-800">
                Volver al Dashboard
            </a>
        </div>
    </div>
</main>

<?php include 'layout/footer.php'; ?>