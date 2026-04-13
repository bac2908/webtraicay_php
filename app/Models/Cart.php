<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get total price of cart items
     */
    public function getTotalPrice()
    {
        return $this->items->sum(fn($item) => $item->qty * $item->unit_price);
    }

    /**
     * Get total items count
     */
    public function getTotalItems()
    {
        return $this->items->sum('qty');
    }
}

