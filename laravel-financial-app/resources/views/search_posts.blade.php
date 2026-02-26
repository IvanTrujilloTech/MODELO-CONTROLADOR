@extends('layout.header')

@section('title', 'Buscar Artículos')
@section('page_title', 'Buscar Artículos')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Buscar Artículos</h1>
            
            <form method="GET" action="{{ route('posts.search') }}" class="mt-4 flex">
                <input type="text" name="q" placeholder="Buscar artículos..." value="{{ request()->get('q', '') }}" class="flex-1 border border-gray-300 rounded-l-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-r-md focus:outline-none focus:shadow-outline">
                    Buscar
                </button>
            </form>
        </div>
        
        @if(request()->get('q'))
            <div class="mb-8">
                <p class="text-lg text-gray-700">Resultados para: "{{ request()->get('q') }}"</p>
            </div>
        @endif
        
        @if($results->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="mt-2 text-sm font-medium text-gray-900">No se encontraron artículos</p>
                <p class="mt-1 text-sm text-gray-500">Intenta buscar con otros términos</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($results as $post)
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
        @endif
        
        <div class="mt-8">
            <a href="{{ route('posts') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                ← Volver al blog
            </a>
        </div>
    </div>
@endsection

@extends('layout.footer')
