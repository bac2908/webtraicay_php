<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'url',
        'sort_order',
    ];

    /**
     * Get the coupon that owns the image
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
