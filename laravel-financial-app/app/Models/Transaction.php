<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    
    protected $fillable = [
        'user_id',
        'type',
        'category',
        'amount',
        'description',
        'date',
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public static function getBalance($user_id)
    {
        return self::where('user_id', $user_id)
            ->selectRaw('SUM(CASE WHEN type = "ingreso" THEN amount ELSE 0 END) - SUM(CASE WHEN type = "gasto" THEN amount ELSE 0 END) as balance')
            ->value('balance') ?? 0;
    }
}
