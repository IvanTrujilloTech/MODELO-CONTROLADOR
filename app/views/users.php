<?php include 'layout/header.php'; ?>

<main class="container mx-auto py-12 px-4">
    <h2 class="text-3xl font-bold mb-8 text-gray-800">Usuarios Registrados</h2>

    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Lista de Usuarios</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Registro</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900"><?php echo $user['id']; ?></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900"><?php echo $user['nombre']; ?></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900"><?php echo $user['email']; ?></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900"><?php echo $user['created_at']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if(empty($users)): ?>
        <p class="text-gray-500 mt-4">No hay usuarios registrados aún.</p>
        <?php endif; ?>
    </div>
</main>

<?php include 'layout/footer.php'; ?>