<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';
    
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'timestamp',
    ];
    
    protected $casts = [
        'timestamp' => 'datetime',
    ];
    
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    
    public static function getMessagesBetweenUsers($user1, $user2)
    {
        return self::where(function ($query) use ($user1, $user2) {
            $query->where('sender_id', $user1)
                  ->where('receiver_id', $user2);
        })->orWhere(function ($query) use ($user1, $user2) {
            $query->where('sender_id', $user2)
                  ->where('receiver_id', $user1);
        })->orderBy('timestamp', 'asc')->get();
    }
}
