<?php include 'layout/header.php'; ?>

<main class="container mx-auto py-12 px-4">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">registro de usuario</h2>
        
        <?php if (isset($_get['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php 
                $error = htmlspecialchars($_get['error']);
                echo "error: " . $error; 
                ?>
            </div>
        <?php endif; ?>
        
        <form action="/register" method="post" id="registerform">
            <input type="hidden" name="csrf_token" value="<?php echo security::generate_csrf_token(); ?>">
            
            <div class="mb-4">
                <label for="nombre" class="block text-gray-700 text-sm font-bold mb-2">nombre:</label>
                <input type="text" 
                       id="nombre" 
                       name="nombre" 
                       required
                       maxlength="100"
                       pattern="[a-za-z0-9\saeiou]{2,100}"
                       title="solo letras, numeros y espacios (2-100 caracteres)"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">email:</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       required
                       maxlength="255"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">contrasena:</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       minlength="8"
                       maxlength="128"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 mt-1">minimo 8 caracteres</p>
            </div>
            
            <div class="flex items-center justify-between">
                <button type="submit" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    registrarse
                </button>
                <a href="/login" class="inline-block align-baseline font-bold text-sm text-emerald-600 hover:text-emerald-800">
                    ya tienes cuenta? inicia sesion
                </a>
            </div>
        </form>
    </div>
</main>

<script>
document.getelementbyid('registerform').addeventlistener('submit', function(e) {
    const password = document.getelementbyid('password').value;
    const nombre = document.getelementbyid('nombre').value;
    
    // validacion en cliente
    if (password.length < 8) {
        e.preventdefault();
        alert('la contrasena debe tener al menos 8 caracteres');
        return false;
    }
    
    if (!/^[a-za-z0-9\saeiou]{2,100}$/.test(nombre)) {
        e.preventdefault();
        alert('el nombre solo puede contener letras, numeros y espacios');
        return false;
    }
    
    return true;
});
</script>

<?php include 'layout/footer.php'; ?>
