<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'unit',
        'stock',
        'price',
        'sale_price',
        'thumb',
        'short_desc',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'integer',
        'sale_price' => 'integer',
        'stock' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the actual price (sale_price if available, otherwise price)
     */
    public function getActualPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Get discount amount
     */
    public function getDiscountAmountAttribute()
    {
        return $this->sale_price ? $this->price - $this->sale_price : 0;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentAttribute()
    {
        if (!$this->sale_price || $this->price == 0) {
            return 0;
        }
        return round(($this->price - $this->sale_price) / $this->price * 100);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price');
    }

    public function getThumbUrlAttribute(): string
    {
        $thumb = trim((string) ($this->thumb ?? ''));

        if ($thumb === '') {
            return '//theme.hstatic.net/200000157781/1001036201/14/no-image.jpg?v=1064';
        }

        if (Str::startsWith($thumb, ['http://', 'https://', '//'])) {
            return $thumb;
        }

        $path = ltrim($thumb, '/');
        $rootUrl = rtrim((string) url('/'), '/');

        // When the app is opened through server.php, asset() can generate server.php/images/... URLs.
        // In that case we point directly to the public directory where images actually exist.
        if (Str::endsWith(Str::lower($rootUrl), '/server.php')) {
            $base = preg_replace('#/server\.php$#i', '', $rootUrl) ?: $rootUrl;

            if (!Str::endsWith(Str::lower($base), '/public')) {
                $base .= '/public';
            }

            return rtrim($base, '/') . '/' . $path;
        }

        return asset($path);
    }
}

