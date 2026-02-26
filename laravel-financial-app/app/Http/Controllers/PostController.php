<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show', 'search']);
    }
    
    public function index()
    {
        $page = request()->get('page', 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $posts = Post::orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        
        return view('posts', ['posts' => $posts]);
    }
    
    public function show($id)
    {
        $post = Post::findOrFail($id);
        $relatedPosts = $post->getRelatedPosts();
        
        return view('post_detail', [
            'post' => $post,
            'relatedPosts' => $relatedPosts
        ]);
    }
    
    public function create()
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            return redirect()->route('posts')->with('error', 'No tienes permisos para crear posts');
        }
        
        return view('create_post');
    }
    
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            return redirect()->route('posts')->with('error', 'No tienes permisos para crear posts');
        }
        
        $validatedData = $request->validate([
            'titulo' => 'required|string|min:5|max:255',
            'contenido' => 'required|string|min:50',
            'resumen' => 'nullable|string',
            'categoria' => 'required|in:Finanzas,Inversiones,Ahorro,Educación,Tecnología',
            'tags' => 'nullable|string',
            'imagen' => 'nullable|url',
        ]);
        
        $post = Post::create([
            'title' => $validatedData['titulo'],
            'content' => $validatedData['contenido'],
            'summary' => $validatedData['resumen'] ?? '',
            'category' => $validatedData['categoria'],
            'tags' => $validatedData['tags'] ?? '',
            'image' => $validatedData['imagen'] ?? '',
            'author_id' => $user->id,
        ]);
        
        $this->sendNewPostNotification($post);
        
        return redirect()->route('post.show', $post->id)->with('success', 'Post creado correctamente');
    }
    
    public function edit($id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            return redirect()->route('posts')->with('error', 'No tienes permisos para editar posts');
        }
        
        $post = Post::findOrFail($id);
        return view('edit_post', ['post' => $post]);
    }
    
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            return redirect()->route('posts')->with('error', 'No tienes permisos para editar posts');
        }
        
        $post = Post::findOrFail($id);
        
        $validatedData = $request->validate([
            'titulo' => 'required|string|min:5|max:255',
            'contenido' => 'required|string|min:50',
            'resumen' => 'nullable|string',
            'categoria' => 'required|in:Finanzas,Inversiones,Ahorro,Educación,Tecnología',
            'tags' => 'nullable|string',
            'imagen' => 'nullable|url',
        ]);
        
        $post->update([
            'title' => $validatedData['titulo'],
            'content' => $validatedData['contenido'],
            'summary' => $validatedData['resumen'] ?? '',
            'category' => $validatedData['categoria'],
            'tags' => $validatedData['tags'] ?? '',
            'image' => $validatedData['imagen'] ?? '',
        ]);
        
        return redirect()->route('post.show', $post->id)->with('success', 'Post actualizado correctamente');
    }
    
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            return redirect()->route('posts')->with('error', 'No tienes permisos para eliminar posts');
        }
        
        $post = Post::findOrFail($id);
        $post->delete();
        
        return redirect()->route('posts')->with('success', 'Post eliminado correctamente');
    }
    
    public function search()
    {
        $query = request()->get('q', '');
        $results = [];
        
        if (!empty($query)) {
            $results = Post::search($query);
        }
        
        return view('search_posts', [
            'query' => $query,
            'results' => $results
        ]);
    }
    
    private function sendNewPostNotification($post)
    {
        $postData = [
            'titulo' => $post->title,
            'resumen' => $post->summary,
            'categoria' => $post->category,
            'imagen' => $post->image,
            'autor_id' => $post->author_id,
            'url' => request()->getHost() . "/posts/" . $post->id,
        ];
        
        $webhookUrl = config('app.n8n_webhook_url') ?: 'https://ivantrubar.app.n8n.cloud/webhook-test/ff373657-1ce7-4512-9329-1b534d87c759';
        
        if ($webhookUrl) {
            $payload = [
                'event' => 'new_post_published',
                'timestamp' => now()->toISOString(),
                'data' => $postData,
                'metadata' => [
                    'source' => 'laravel-financial-app',
                    'version' => '1.0',
                ],
            ];
            
            try {
                $client = new \GuzzleHttp\Client();
                $client->post($webhookUrl, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-Webhook-Source' => 'laravel-financial-app',
                    ],
                    'json' => $payload,
                    'timeout' => 30,
                ]);
            } catch (\Exception $e) {
                error_log('Webhook error: ' . $e->getMessage());
            }
        }
    }
}
