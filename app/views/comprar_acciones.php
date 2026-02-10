<?php include 'layout/header.php'; ?>

<main class="container mx-auto py-12 px-4">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">comprar acciones</h2>
        
        <?php if (isset($_get['error'])): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php 
                $error = htmlspecialchars($_get['error']);
                switch ($error) {
                    case 'insufficient_balance':
                        echo 'saldo insuficiente para realizar la compra.';
                        break;
                    case 'invalid_amount':
                        echo 'cantidad o precio invalido.';
                        break;
                    case 'amount_too_high':
                        echo 'el monto excede el limite permitido.';
                        break;
                    case 'invalid_company':
                        echo 'nombre de empresa invalido.';
                        break;
                    default:
                        echo 'error al procesar la solicitud.';
                }
                ?>
            </div>
        <?php endif; ?>
        
        <form action="/comprar-acciones" method="post" id="buyform">
            <input type="hidden" name="csrf_token" value="<?php echo security::generate_csrf_token(); ?>">
            
            <div class="mb-4">
                <label for="empresa" class="block text-gray-700 text-sm font-bold mb-2">empresa:</label>
                <input type="text" 
                       id="empresa" 
                       name="empresa" 
                       required
                       maxlength="100"
                       pattern="[a-za-z0-9\s.]{1,100}"
                       title="solo letras, numeros, espacios y puntos"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       placeholder="ej: aapl">
            </div>
            
            <div class="mb-4">
                <label for="cantidad" class="block text-gray-700 text-sm font-bold mb-2">cantidad:</label>
                <input type="number" 
                       id="cantidad" 
                       name="cantidad" 
                       required
                       min="1"
                       max="1000000"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                       min="1">
            </div>
            
            <div class="mb-6">
                <label for="precio_compra" class="block text-gray-700 text-sm font-bold mb-2">precio compra (eur):</label>
                <input type="number" 
                       step="0.01" 
                       min="0.01"
                       max="999999.99"
                       id="precio_compra" 
                       name="precio_compra" 
                       required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    comprar acciones
                </button>
                <a href="/inversiones" class="inline-block align-baseline font-bold text-sm text-emerald-600 hover:text-emerald-800">
                    cancelar
                </a>
            </div>
        </form>
    </div>
</main>

<script>
document.getelementbyid('buyform').addeventlistener('submit', function(e) {
    const cantidad = parseint(document.getelementbyid('cantidad').value);
    const precio = parsefloat(document.getelementbyid('precio_compra').value);
    const empresa = document.getelementbyid('empresa').value;
    
    if (isnan(cantidad) || cantidad < 1) {
        e.preventdefault();
        alert('la cantidad debe ser al menos 1');
        return false;
    }
    
    if (isnan(precio) || precio <= 0) {
        e.preventdefault();
        alert('el precio debe ser mayor a 0');
        return false;
    }
    
    if (!/^[a-za-z0-9\s.]{1,100}$/.test(empresa)) {
        e.preventdefault();
        alert('nombre de empresa invalido');
        return false;
    }
    
    const total = cantidad * precio;
    if (total > 999999999.99) {
        e.preventdefault();
        alert('el total excede el limite permitido');
        return false;
    }
    
    return true;
});
</script>

<?php include 'layout/footer.php'; ?>
