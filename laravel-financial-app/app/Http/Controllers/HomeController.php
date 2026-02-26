<?php

namespace App\Http\Controllers;

use App\Models\Post;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        // Get latest blog posts for preview
        $latestPosts = Post::orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        return view('welcome', [
            'latestPosts' => $latestPosts
        ]);
    }
}
