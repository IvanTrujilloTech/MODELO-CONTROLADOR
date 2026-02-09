<?php include 'layout/header.php'; ?>

<main class="container mx-auto py-12 px-4">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">anadir transaccion</h2>
        
        <?php if (isset($_get['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($_get['error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="/add-transaction" method="post" id="transactionform">
            <input type="hidden" name="csrf_token" value="<?php echo security::generate_csrf_token(); ?>">
            
            <div class="mb-4">
                <label for="tipo" class="block text-gray-700 text-sm font-bold mb-2">tipo:</label>
                <select id="tipo" name="tipo" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="ingreso">ingreso</option>
                    <option value="gasto">gasto</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="categoria" class="block text-gray-700 text-sm font-bold mb-2">categoria:</label>
                <select id="categoria" name="categoria" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="salario">salario</option>
                    <option value="alquiler">alquiler</option>
                    <option value="comida">comida</option>
                    <option value="transporte">transporte</option>
                    <option value="entretenimiento">entretenimiento</option>
                    <option value="inversiones">inversiones</option>
                    <option value="otros">otros</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="monto" class="block text-gray-700 text-sm font-bold mb-2">monto (eur):</label>
                <input type="number" 
                       step="0.01" 
                       min="0.01" 
                       max="999999999.99"
                       id="monto" 
                       name="monto" 
                       required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label for="descripcion" class="block text-gray-700 text-sm font-bold mb-2">descripcion:</label>
                <input type="text" 
                       id="descripcion" 
                       name="descripcion" 
                       maxlength="255"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-6">
                <label for="fecha" class="block text-gray-700 text-sm font-bold mb-2">fecha:</label>
                <input type="date" 
                       id="fecha" 
                       name="fecha" 
                       value="<?php echo security::sanitize_string(date('y-m-d')); ?>" 
                       required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    anadir transaccion
                </button>
                <a href="/dashboard" class="inline-block align-baseline font-bold text-sm text-emerald-600 hover:text-emerald-800">
                    cancelar
                </a>
            </div>
        </form>
    </div>
</main>

<script>
document.getelementbyid('transactionform').addeventlistener('submit', function(e) {
    const monto = parsefloat(document.getelementbyid('monto').value);
    
    if (isnan(monto) || monto <= 0) {
        e.preventdefault();
        alert('el monto debe ser mayor a 0');
        return false;
    }
    
    if (monto > 999999999.99) {
        e.preventdefault();
        alert('el monto excede el limite permitido');
        return false;
    }
    
    return true;
});
</script>

<?php include 'layout/footer.php'; ?>
