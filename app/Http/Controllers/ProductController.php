<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $products = $this->productService->getCollection($request);
        $allCategories = $this->productService->getCategories();
        $featuredProducts = $this->productService->getFeaturedProducts(5);

        return view('products.index', [
            'products'          => $products,
            'allCategories'     => $allCategories,
            'featuredProducts'  => $featuredProducts,
        ]);
    }

    public function show($slug)
    {
        $product = $this->productService->findBySlug($slug);

        if (!$product) {
            abort(404);
        }

        $relatedProducts = $this->productService->getRelatedProducts($product, 8);
        $reviewSamples = [
            [
                'name' => 'Khach hang da mua',
                'rating' => 5,
                'content' => 'Trai cay tuoi, dong goi dep va giao dung hen. Se tiep tuc ung ho.',
                'created_at' => '2 ngay truoc',
            ],
            [
                'name' => 'Chi Thanh',
                'rating' => 4,
                'content' => 'San pham dung mo ta, qua dep. Ho tro doi mau qua nhanh khi can gap.',
                'created_at' => '1 tuan truoc',
            ],
        ];

        return view('products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'reviewSamples' => $reviewSamples,
        ]);
    }

    public function search(Request $request)
    {
        $keyword = trim((string) $request->get('q', ''));

        if ($keyword === '') {
            return redirect()->route('products.index');
        }

        $products = $this->productService->search($keyword);
        $allCategories = $this->productService->getCategories();
        $featuredProducts = $this->productService->getFeaturedProducts(5);

        return view('products.index', [
            'products' => $products,
            'allCategories' => $allCategories,
            'featuredProducts' => $featuredProducts,
            'searchKeyword' => $keyword,
        ]);
    }
}
