@extends('layout.header')

@section('title', 'Nueva Transferencia')
@section('page_title', 'Nueva Transferencia')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-6">Nueva Transferencia</h3>
            
            <form method="POST" action="{{ route('transfers.store') }}">
                @csrf
                
                <div class="mb-4">
                    <label for="recipient_id" class="block text-sm font-medium text-gray-700">Destinatario</label>
                    <select id="recipient_id" name="recipient_id" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Selecciona un destinatario</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700">Monto (€)</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0.01" max="999999.99" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea id="description" name="description" maxlength="200" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" rows="3"></textarea>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Realizar Transferencia
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layout.footer')
