@extends('layout.header')

@section('title', 'Crear Artículo')
@section('page_title', 'Crear Artículo')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Crear Nuevo Artículo</h1>
            
            <form method="POST" action="{{ route('posts.store') }}">
                @csrf
                
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Contenido</label>
                    <textarea id="content" name="content" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" rows="12">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                    <select id="category" name="category" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Selecciona una categoría</option>
                        <option value="Finanzas" {{ old('category') == 'Finanzas' ? 'selected' : '' }}>Finanzas</option>
                        <option value="Inversiones" {{ old('category') == 'Inversiones' ? 'selected' : '' }}>Inversiones</option>
                        <option value="Ahorro" {{ old('category') == 'Ahorro' ? 'selected' : '' }}>Ahorro</option>
                        <option value="Educación" {{ old('category') == 'Educación' ? 'selected' : '' }}>Educación</option>
                        <option value="Tecnología" {{ old('category') == 'Tecnología' ? 'selected' : '' }}>Tecnología</option>
                    </select>
                    @error('category')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Etiquetas (separadas por comas)</label>
                    <input type="text" id="tags" name="tags" value="{{ old('tags') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('tags')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Imagen (URL)</label>
                    <input type="url" id="image" name="image" value="{{ old('image') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('image')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end">
                    <a href="{{ route('posts') }}" class="mr-4 bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Crear Artículo
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@extends('layout.footer')
