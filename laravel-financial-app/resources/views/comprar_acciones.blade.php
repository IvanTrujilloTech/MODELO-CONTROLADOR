@extends('layout.header')

@section('title', 'Comprar Acciones')
@section('page_title', 'Comprar Acciones')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-6">Comprar Acciones</h3>
            
            <form method="POST" action="{{ route('investments.store') }}">
                @csrf
                
                <div class="mb-4">
                    <label for="empresa" class="block text-sm font-medium text-gray-700">Empresa</label>
                    <input type="text" id="empresa" name="empresa" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                
                <div class="mb-4">
                    <label for="cantidad" class="block text-sm font-medium text-gray-700">Cantidad de acciones</label>
                    <input type="number" id="cantidad" name="cantidad" min="1" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                
                <div class="mb-4">
                    <label for="precio_compra" class="block text-sm font-medium text-gray-700">Precio por acción (€)</label>
                    <input type="number" id="precio_compra" name="precio_compra" step="0.01" min="0.01" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                
                <div class="mb-6">
                    <div class="bg-emerald-50 border-l-4 border-emerald-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-emerald-700" id="total_amount">Total: €0.00</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Comprar Acciones
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Calculate total amount when inputs change
        document.getElementById('cantidad').addEventListener('input', calculateTotal);
        document.getElementById('precio_compra').addEventListener('input', calculateTotal);
        
        function calculateTotal() {
            const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
            const precio = parseFloat(document.getElementById('precio_compra').value) || 0;
            const total = cantidad * precio;
            
            document.getElementById('total_amount').textContent = 'Total: €' + total.toFixed(2);
        }
    </script>
@endsection

@extends('layout.footer')
