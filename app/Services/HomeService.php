<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class HomeService
{
    /**
     * Get data for homepage sections based on category slugs
     */
    public function getHomeSections(array $slugs, int $productLimit = 12)
    {
        return Category::query()
            ->whereIn('slug', $slugs)
            ->orderBy(\Illuminate\Support\Facades\DB::raw("FIELD(slug, '" . implode("','", $slugs) . "')"))
            ->get()
            ->map(function (Category $category) use ($productLimit) {
                $products = Product::query()
                    ->where('is_active', true)
                    ->where('category_id', $category->id)
                    ->orderByDesc('id')
                    ->limit($productLimit)
                    ->get();

                return [
                    'category' => $category,
                    'products' => $products,
                    'icon_url' => $category->getIconUrl(), // Add icon URL from model method
                ];
            });
    }

    /**
     * Get top level active categories
     */
    public function getTopCategories()
    {
        return Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get active coupons with images
     */
    public function getActiveCoupons(int $limit = 6)
    {
        return Coupon::query()
            ->with('images')
            ->where('is_active', true)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }
}
