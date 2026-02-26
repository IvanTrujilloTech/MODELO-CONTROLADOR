<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $table = 'transfers';
    
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'amount',
        'description',
        'timestamp',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'timestamp' => 'datetime',
    ];
    
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
    
    public static function getTransfersForUser($user_id)
    {
        return self::where('sender_id', $user_id)
            ->orWhere('recipient_id', $user_id)
            ->orderBy('timestamp', 'desc')
            ->get();
    }
}
