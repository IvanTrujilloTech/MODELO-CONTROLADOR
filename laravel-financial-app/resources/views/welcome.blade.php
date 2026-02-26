<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finanzas Personales - Gestión Inteligente de Tu Dinero</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Header -->
    <header class="gradient-bg text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Finanzas Personales</h1>
                <p class="text-xl md:text-2xl mb-8 opacity-90">Gestiona tus finanzas con inteligencia y control</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-white text-emerald-700 hover:bg-gray-100 font-bold py-3 px-6 rounded-lg transition-colors">
                            Ir al Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="bg-white text-emerald-700 hover:bg-gray-100 font-bold py-3 px-6 rounded-lg transition-colors">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" class="bg-emerald-800 text-white hover:bg-emerald-900 font-bold py-3 px-6 rounded-lg transition-colors">
                            Registrarse
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Todo lo que necesitas para tus finanzas</h2>
                <p class="text-gray-600 max-w-3xl mx-auto">Plataforma completa para gestionar tus ingresos, gastos, inversiones y más</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white rounded-xl shadow-md p-8 border border-gray-100">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Control de Finanzas</h3>
                    <p class="text-gray-600">Registra y categoriza tus ingresos y gastos para tener un control total de tu presupuesto.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card bg-white rounded-xl shadow-md p-8 border border-gray-100">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Inversiones Inteligentes</h3>
                    <p class="text-gray-600">Gestiona tu portafolio de inversiones y compra/vende acciones directamente desde la plataforma.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card bg-white rounded-xl shadow-md p-8 border border-gray-100">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Comunidad Financiera</h3>
                    <p class="text-gray-600">Comparte consejos y experiencias con otros usuarios en nuestra sala de chat y blog financiero.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Preview Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Blog Financiero</h2>
                <p class="text-gray-600 max-w-3xl mx-auto">Artículos y consejos para mejorar tu salud financiera</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($latestPosts as $post)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        @if($post->image)
                            <img src="{{ $post->image }}" alt="{{ $post->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                    {{ $post->category }}
                                </span>
                                <span class="ml-2 text-sm text-gray-500">{{ $post->created_at->format('d/m/Y') }}</span>
                            </div>
                            
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $post->title }}</h3>
                            
                            @if($post->summary)
                                <p class="text-gray-600 mb-4">{{ $post->summary }}</p>
                            @endif
                            
                            <a href="{{ route('post.show', $post->id) }}" class="text-emerald-600 hover:text-emerald-700 font-medium">
                                Leer más →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('posts') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-lg transition-colors">
                    Ver todos los artículos
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Finanzas Personales</h3>
                    <p class="text-gray-400">Tu aliado para una gestión financiera inteligente y responsable.</p>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="{{ route('posts') }}" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition-colors">Dashboard</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition-colors">Iniciar Sesión</a></li>
                            <li><a href="{{ route('register') }}" class="text-gray-400 hover:text-white transition-colors">Registrarse</a></li>
                        @endauth
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contacto</h4>
                    <p class="text-gray-400">Email: contacto@finanzas-personales.com</p>
                    <p class="text-gray-400">Teléfono: +34 123 456 789</p>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>© {{ date('Y') }} Finanzas Personales. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>
