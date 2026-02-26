<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';
    
    protected $fillable = [
        'title',
        'content',
        'summary',
        'category',
        'tags',
        'image',
        'author_id',
    ];
    
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    
    public function getRelatedPosts($limit = 3)
    {
        $keywords = $this->extractKeywords($this->content);
        $relatedPosts = self::where('id', '!=', $this->id)
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('title', 'like', "%{$keyword}%")
                          ->orWhere('content', 'like', "%{$keyword}%")
                          ->orWhere('tags', 'like', "%{$keyword}%")
                          ->orWhere('category', 'like', "%{$keyword}%");
                }
            })
            ->orWhere('category', $this->category)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        
        return $relatedPosts;
    }
    
    public function extractKeywords($content, $limit = 5)
    {
        $stopwords = [
            'el', 'la', 'los', 'las', 'un', 'una', 'unos', 'unas', 'de', 'del', 'al',
            'en', 'por', 'para', 'con', 'sin', 'sobre', 'entre', 'y', 'e', 'o', 'u',
            'que', 'cual', 'cuales', 'como', 'cuando', 'donde', 'quien', 'es', 'son',
            'este', 'esta', 'estos', 'estas', 'ese', 'esa', 'esos', 'esas', 'aquel',
            'aquella', 'aquellos', 'aquellas', 'lo', 'le', 'les', 'se', 'si', 'no',
            'pero', 'mas', 'muy', 'ya', 'todo', 'todos', 'toda', 'todas', 'mi', 'tu',
            'su', 'mis', 'tus', 'sus', 'nuestro', 'nuestra', 'nuestros', 'nuestras',
            'your', 'the', 'and', 'or', 'but', 'is', 'are', 'was', 'were', 'be', 'been',
            'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could'
        ];
        
        $content = strtolower(strip_tags($content));
        $content = preg_replace('/[^\p{l}\p{n}\s]/u', ' ', $content);
        $words = preg_split('/\s+/', $content);
        
        $wordcount = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) > 2 && !in_array($word, $stopwords) && !is_numeric($word)) {
                $wordcount[$word] = ($wordcount[$word] ?? 0) + 1;
            }
        }
        
        arsort($wordcount);
        
        return array_slice(array_keys($wordcount), 0, $limit);
    }
    
    public static function search($query, $limit = 10)
    {
        $searchterm = "%{$query}%";
        return self::where('title', 'like', $searchterm)
            ->orWhere('content', 'like', $searchterm)
            ->orWhere('summary', 'like', $searchterm)
            ->orWhere('tags', 'like', $searchterm)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public static function getCountByCategory()
    {
        return self::select('category', \DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();
    }
}
