<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public function getCollection(Request $request)
    {
        $query = Product::query()->where('is_active', true);

        // Filter by Category
        if ($request->has('category')) {
            $categorySlug = (string) $request->get('category');
            $category = Category::query()
                ->where('slug', $categorySlug)
                ->where('is_active', true)
                ->first();

            if ($category) {
                $categoryIds = [$category->id];
                $childrenIds = Category::query()
                    ->where('parent_id', $category->id)
                    ->where('is_active', true)
                    ->pluck('id')
                    ->all();

                if (!empty($childrenIds)) {
                    $categoryIds = array_merge($categoryIds, $childrenIds);
                }

                $query->whereIn('category_id', array_values(array_unique($categoryIds)));

                $rawTag = trim((string) $request->get('tag', ''));
                if ($rawTag !== '') {
                    $decodedTag = urldecode($rawTag);
                    $tagSlug = Str::slug($decodedTag);

                    $tagCategory = Category::query()
                        ->where('parent_id', $category->id)
                        ->where('is_active', true)
                        ->where(function ($q) use ($tagSlug, $decodedTag) {
                            $q->where('slug', $tagSlug)
                              ->orWhere('name', 'like', '%' . $decodedTag . '%');
                        })
                        ->first();

                    if ($tagCategory) {
                        $query->where('category_id', $tagCategory->id);
                    } else {
                        $query->where('name', 'like', '%' . $decodedTag . '%');
                    }
                }
            }
        }

        // Filter by Price Range
        if ($request->has('price')) {
            $range = explode('-', $request->price);
            if (count($range) == 2) {
                $query->whereBetween(DB::raw('IFNULL(sale_price, price)'), [$range[0], $range[1]]);
            } elseif ($request->price == 'over-1000000') {
                $query->where(DB::raw('IFNULL(sale_price, price)'), '>', 1000000);
            }
        }

        // Sort
        switch ($request->get('sort')) {
            case 'price-asc':
                $query->orderBy(DB::raw('IFNULL(sale_price, price)'), 'asc');
                break;
            case 'price-desc':
                $query->orderBy(DB::raw('IFNULL(sale_price, price)'), 'desc');
                break;
            case 'alpha-asc':
                $query->orderBy('name', 'asc');
                break;
            case 'alpha-desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate(12)->appends($request->all());
    }

    public function find($id)
    {
        return Product::where('is_active', true)->find($id);
    }

    public function findBySlug(string $slug)
    {
        return Product::query()
            ->with([
                'category',
                'images' => function ($q) {
                    $q->orderBy('sort_order');
                },
            ])
            ->where('is_active', true)
            ->where('slug', $slug)
            ->first();
    }

    public function search($query)
    {
        return Product::where('is_active', true)
            ->where('name', 'like', "%$query%")
            ->paginate(12);
    }

    public function getCategories()
    {
        return Category::where('is_active', true)
            ->withCount('products')
            ->get();
    }

    public function getFeaturedProducts($limit = 5)
    {
        return Product::query()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRelatedProducts(Product $product, int $limit = 8)
    {
        $baseQuery = Product::query()
            ->where('is_active', true)
            ->where('id', '!=', $product->id);

        if ($product->category_id) {
            $related = (clone $baseQuery)
                ->where('category_id', $product->category_id)
                ->orderByDesc('id')
                ->limit($limit)
                ->get();

            if ($related->count() >= $limit) {
                return $related;
            }

            $excludeIds = $related->pluck('id')->push($product->id)->all();

            $extra = Product::query()
                ->where('is_active', true)
                ->whereNotIn('id', $excludeIds)
                ->orderByDesc('id')
                ->limit($limit - $related->count())
                ->get();

            return $related->concat($extra);
        }

        return $baseQuery
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }
}
