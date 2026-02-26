@extends('layout.header')

@section('title', $post->title)
@section('page_title', $post->title)

@section('content')
    <div class="max-w-4xl mx-auto">
        <article class="bg-white rounded-xl shadow-md p-8">
            <!-- Article header -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                        {{ $post->category }}
                    </span>
                    <span class="ml-2 text-sm text-gray-500">{{ $post->created_at->format('d/m/Y') }}</span>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $post->title }}</h1>
                
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-700">{{ $post->author->name }}</p>
                        <p class="text-xs text-gray-500">{{ $post->author->email }}</p>
                    </div>
                </div>
                
                @if($post->image)
                    <img src="{{ $post->image }}" alt="{{ $post->title }}" class="w-full h-64 object-cover rounded-lg mb-6">
                @endif
            </div>
            
            <!-- Article content -->
            <div class="mb-8 prose max-w-none">
                {!! nl2br(e($post->content)) !!}
            </div>
            
            <!-- Article tags -->
            @if($post->tags)
                <div class="mb-8">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Etiquetas</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $post->tags) as $tag)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ trim($tag) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Related posts -->
            @if(!$relatedPosts->isEmpty())
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Artículos relacionados</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($relatedPosts as $relatedPost)
                            <div class="bg-gray-50 rounded-lg p-4">
                                @if($relatedPost->image)
                                    <img src="{{ $relatedPost->image }}" alt="{{ $relatedPost->title }}" class="w-full h-32 object-cover rounded-lg mb-3">
                                @endif
                                <h4 class="text-md font-semibold text-gray-800 mb-2">{{ $relatedPost->title }}</h4>
                                <p class="text-sm text-gray-600 mb-3">{{ $relatedPost->summary }}</p>
                                <a href="{{ route('post.show', $relatedPost->id) }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">
                                    Leer más →
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Comments section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Comentarios</h3>
                
                @auth
                    <form method="POST" action="{{ route('comments.store') }}" class="mb-6">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                        
                        <div class="mb-4">
                            <label for="comment" class="block text-sm font-medium text-gray-700">Tu comentario</label>
                            <textarea id="comment" name="comment" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" rows="4"></textarea>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Enviar Comentario
                            </button>
                        </div>
                    </form>
                @else
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <p class="text-gray-600">Debes estar <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-700">registrado</a> para comentar.</p>
                    </div>
                @endif
                
                @if($post->comments->isEmpty())
                    <div class="text-center py-8">
                        <p class="text-gray-500">Aún no hay comentarios.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($post->comments as $comment)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-700">{{ $comment->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $comment->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                <p class="text-gray-600">{{ $comment->comment }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            
            <!-- Admin actions -->
            @auth
                @if(Auth::user()->isAdmin())
                    <div class="border-t pt-6">
                        <div class="flex justify-between">
                            <a href="{{ route('posts.edit', $post->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Editar Artículo
                            </a>
                            
                            <form method="POST" action="{{ route('posts.destroy', $post->id) }}" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este artículo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Eliminar Artículo
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endauth
        </article>
    </div>
@endsection

@extends('layout.footer')
