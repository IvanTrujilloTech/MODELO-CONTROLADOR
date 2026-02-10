<?php include 'layout/header.php'; 
require_once __DIR__ . '/../helpers/SecurityHelper.php';
?>

<main class="container mx-auto py-12 px-4">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 p-6">
<<<<<<< HEAD
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Iniciar Sesión</h2>
        <form action="/login" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo generarCSRFToken(); ?>">
=======
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">iniciar sesion</h2>
        
        <?php if (isset($_get['timeout'])): ?>
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                tu sesion ha expirado. por favor, inicia sesion nuevamente.
            </div>
        <?php endif; ?>
        
        <?php if (isset($_get['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php 
                $error = htmlspecialchars($_get['error']);
                echo "error: " . $error; 
                ?>
            </div>
        <?php endif; ?>
        
        <form action="/login" method="post" id="loginform">
            <input type="hidden" name="csrf_token" value="<?php echo security::generate_csrf_token(); ?>">
            
>>>>>>> 690af805c7e7051399ef07e19437d54d9adcaa49
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
                       minlength="1"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="flex items-center justify-between">
                <button type="submit" 
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    iniciar sesion
                </button>
                <a href="/register" class="inline-block align-baseline font-bold text-sm text-emerald-600 hover:text-emerald-800">
                    no tienes cuenta? registrate
                </a>
            </div>
        </form>
    </div>
</main>

<script>
document.getelementbyid('loginform').addeventlistener('submit', function(e) {
    const email = document.getelementbyid('email').value;
    
    // validacion basica de email
    const emailregex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailregex.test(email)) {
        e.preventdefault();
        alert('por favor, introduce un email valido');
        return false;
    }
    
    return true;
});
</script>

<?php include 'layout/footer.php'; ?>
