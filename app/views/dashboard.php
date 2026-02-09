<?php include 'layout/header.php'; ?>

<main class="container mx-auto py-12 px-4">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Dashboard Financiero</h2>
        <div class="mt-4 md:mt-0">
            <a href="/search" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                🔍 Buscar Transacciones
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Saldo Total</h3>
            <p class="text-3xl font-bold text-emerald-600">€<?php echo number_format($balance, 2); ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Ingresos del Mes</h3>
            <p class="text-3xl font-bold text-green-600">€<?php echo number_format($monthly_income, 2); ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Gastos del Mes</h3>
            <p class="text-3xl font-bold text-red-600">€<?php echo number_format($monthly_expenses, 2); ?></p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Últimas Transacciones</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach(array_slice($transactions, 0, 10) as $transaction): ?>
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900"><?php echo $transaction['fecha']; ?></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $transaction['tipo'] == 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo ucfirst($transaction['tipo']); ?>
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900"><?php echo $transaction['categoria']; ?></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900"><?php echo $transaction['descripcion']; ?></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium <?php echo $transaction['tipo'] == 'ingreso' ? 'text-green-600' : 'text-red-600'; ?>">
                            <?php echo $transaction['tipo'] == 'ingreso' ? '+' : '-'; ?>€<?php echo number_format($transaction['monto'], 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if(empty($transactions)): ?>
        <p class="text-gray-500 mt-4">No hay transacciones registradas aún. <a href="/add-transaction" class="text-emerald-600">Añadir primera transacción</a></p>
        <?php else: ?>
        <p class="text-gray-500 mt-4"><a href="/add-transaction" class="text-emerald-600">Añadir nueva transacción</a></p>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border border-red-200 mt-8">
        <h3 class="text-xl font-semibold text-red-700 mb-4">⚠️ Zona de Pruebas</h3>
        <p class="text-gray-600 mb-4">Esta función elimina todas las transacciones e inversiones del usuario actual. Úsala solo para testing.</p>
        <a href="/reset" onclick="return confirm('¿Estás seguro? Esto eliminará todas tus transacciones e inversiones.')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            Resetear Datos de Prueba
        </a>
    </div>
</main>

<?php include 'layout/footer.php'; ?>