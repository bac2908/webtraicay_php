<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Hiển thị trang danh mục sản phẩm
     */
    public function show(Request $request, string $slug, ?string $tag = null)
    {
        $category = Category::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'children' => function ($q) {
                    $q->where('is_active', true)->orderBy('sort_order');
                },
            ])
            ->firstOrFail();

        $selectedTag = $tag ? urldecode($tag) : null;
        $request->merge([
            'category' => $slug,
            'tag' => $selectedTag,
        ]);

        $products = $this->productService->getCollection($request);
        $allCategories = $this->productService->getCategories();
        $featuredProducts = $this->productService->getFeaturedProducts(5);
        $childTags = $category->children;

        return view('products.index', compact('category', 'products', 'allCategories', 'featuredProducts', 'selectedTag', 'childTags'));
    }
}
