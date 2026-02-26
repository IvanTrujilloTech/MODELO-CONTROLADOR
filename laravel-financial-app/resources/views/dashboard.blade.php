@extends('layout.header')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Financiero')

@section('content')
    <div class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Financiero</h1>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('posts.search') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    🔍 Buscar Transacciones
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Saldo Total</h3>
            <p class="text-3xl font-bold text-emerald-600">€{{ number_format($balance, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Ingresos del Mes</h3>
            <p class="text-3xl font-bold text-green-600">€{{ number_format($monthly_income, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Gastos del Mes</h3>
            <p class="text-3xl font-bold text-red-600">€{{ number_format($monthly_expenses, 2) }}</p>
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
                    @foreach(array_slice($transactions->toArray(), 0, 10) as $transaction)
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $transaction['date'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction['type'] == 'ingreso' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($transaction['type']) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $transaction['category'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $transaction['description'] }}</td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium {{ $transaction['type'] == 'ingreso' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction['type'] == 'ingreso' ? '+' : '-' }}€{{ number_format($transaction['amount'], 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($transactions->isEmpty())
        <p class="text-gray-500 mt-4">No hay transacciones registradas aún. <a href="{{ route('transactions.create') }}" class="text-emerald-600">Añadir primera transacción</a></p>
        @else
        <p class="text-gray-500 mt-4"><a href="{{ route('transactions.create') }}" class="text-emerald-600">Añadir nueva transacción</a></p>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-md p-6 border border-red-200 mt-8">
        <h3 class="text-xl font-semibold text-red-700 mb-4">⚠️ Zona de Pruebas</h3>
        <p class="text-gray-600 mb-4">Esta función elimina todas las transacciones e inversiones del usuario actual. Úsala solo para testing.</p>
        <form method="POST" action="{{ route('reset') }}">
            @csrf
            <button type="submit" onclick="return confirm('¿Estás seguro? Esto eliminará todas tus transacciones e inversiones.')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Resetear Datos de Prueba
            </button>
        </form>
    </div>
@endsection

@extends('layout.footer')
