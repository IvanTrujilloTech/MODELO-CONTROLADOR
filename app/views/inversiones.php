<?php include 'layout/header.php'; ?>

<main class="container mx-auto py-12 px-4">
    <h2 class="text-3xl font-bold mb-8 text-gray-800">Mis Inversiones</h2>

    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Acciones Compradas</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Compra</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Compra</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach($inversiones as $inversion): ?>
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900"><?php echo $inversion['empresa']; ?></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900"><?php echo $inversion['cantidad']; ?></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">€<?php echo number_format($inversion['precio_compra'], 2); ?></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900"><?php echo $inversion['fecha_compra']; ?></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-emerald-600">
                            €<?php echo number_format($inversion['cantidad'] * $inversion['precio_compra'], 2); ?>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                            <form action="/vender-acciones" method="POST" class="inline-flex items-center space-x-2">
                                <input type="hidden" name="inversion_id" value="<?php echo $inversion['id']; ?>">
                                <input type="number" name="cantidad" placeholder="Cant." min="1" max="<?php echo $inversion['cantidad']; ?>" step="1" class="w-16 px-2 py-1 border border-gray-300 rounded text-sm" required>
                                <input type="number" name="precio_venta" placeholder="Precio" min="0.01" step="0.01" class="w-20 px-2 py-1 border border-gray-300 rounded text-sm" required>
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Vender</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if(empty($inversiones)): ?>
        <p class="text-gray-500 mt-4">No hay inversiones registradas aún. <a href="/comprar-acciones" class="text-emerald-600">Comprar primeras acciones</a></p>
        <?php else: ?>
        <p class="text-gray-500 mt-4"><a href="/comprar-acciones" class="text-emerald-600">Comprar más acciones</a></p>
        <?php endif; ?>
    </div>
</main>

<?php include 'layout/footer.php'; ?>