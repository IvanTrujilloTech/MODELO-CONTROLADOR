<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    protected $table = 'investments';
    
    protected $fillable = [
        'user_id',
        'company',
        'quantity',
        'purchase_price',
        'purchase_date',
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'purchase_price' => 'decimal:2',
        'purchase_date' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function sell($quantity_to_sell)
    {
        $new_quantity = $this->quantity - $quantity_to_sell;
        
        if ($new_quantity <= 0) {
            return $this->delete();
        } else {
            return $this->update(['quantity' => $new_quantity]);
        }
    }
}
