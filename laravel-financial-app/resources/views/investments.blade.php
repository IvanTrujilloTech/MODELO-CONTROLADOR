@extends('layout.header')

@section('title', 'Inversiones')
@section('page_title', 'Mis Inversiones')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Mis Inversiones</h1>
            <a href="{{ route('investments.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                + Nueva Inversión
            </a>
        </div>
        
        @if($investments->isEmpty())
            <div class="bg-white rounded-xl shadow-md p-6 text-center">
                <p class="text-gray-500 mb-4">No tienes inversiones aún.</p>
                <a href="{{ route('investments.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Comenzar a Invertir
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($investments as $investment)
                    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $investment->company }}</h3>
                                <p class="text-sm text-gray-500">Comprado el {{ $investment->purchase_date->format('d/m/Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-gray-900">{{ $investment->quantity }} acciones</p>
                                <p class="text-sm text-gray-500">€{{ number_format($investment->purchase_price, 2) }}/acción</p>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Coste total</p>
                            <p class="text-xl font-semibold text-gray-900">€{{ number_format($investment->quantity * $investment->purchase_price, 2) }}</p>
                        </div>
                        
                        <form method="POST" action="{{ route('investments.sell') }}">
                            @csrf
                            <input type="hidden" name="inversion_id" value="{{ $investment->id }}">
                            
                            <div class="mb-4">
                                <label for="cantidad-{{ $investment->id }}" class="block text-sm font-medium text-gray-700">Cantidad a vender</label>
                                <input type="number" id="cantidad-{{ $investment->id }}" name="cantidad" min="1" max="{{ $investment->quantity }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            
                            <div class="mb-4">
                                <label for="precio-{{ $investment->id }}" class="block text-sm font-medium text-gray-700">Precio de venta (€)</label>
                                <input type="number" id="precio-{{ $investment->id }}" name="precio_venta" step="0.01" min="0.01" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Vender Acciones
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@extends('layout.footer')
