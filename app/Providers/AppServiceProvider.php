<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Services\HomeService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        // Ensure shared layout data exists on every page using layouts.app
        View::composer('layouts.app', function ($view) {
            $viewData = $view->getData();

            if (array_key_exists('sections', $viewData)) {
                $sections = $viewData['sections'];
            } else {
                $categorySlugs = config('shop.home_categories', []);
                $sections = app(HomeService::class)->getHomeSections($categorySlugs);
            }

            $categorySlugs = config('shop.home_categories', []);

            $megaMenuSlugs = array_slice($categorySlugs, 0, 4);
            $megaCategories = collect();

            if (!empty($megaMenuSlugs)) {
                $slugOrderSql = "FIELD(slug, '" . implode("','", $megaMenuSlugs) . "')";

                $megaCategories = Category::query()
                    ->whereIn('slug', $megaMenuSlugs)
                    ->whereNull('parent_id')
                    ->where('is_active', true)
                    ->with([
                        'children' => function ($query) {
                            $query->where('is_active', true)
                                ->withCount([
                                    'products as active_products_count' => function ($productQuery) {
                                        $productQuery->where('is_active', true);
                                    },
                                ])
                                ->orderByDesc('active_products_count')
                                ->orderBy('sort_order')
                                ->orderBy('name');
                        }
                    ])
                    ->orderByRaw($slugOrderSql)
                    ->get();

                if ($megaCategories->isNotEmpty()) {
                    $rootCategoryIds = $megaCategories->pluck('id')->all();

                    $presetFallbackMenuBySlug = [
                        'trai-cay-thai-lan' => [
                            ['label' => 'Chà là', 'keyword' => 'cha-la'],
                            ['label' => 'Na Thái', 'keyword' => 'na-thai'],
                            ['label' => 'Măng cụt', 'keyword' => 'mang-cut'],
                            ['label' => 'Quýt Thái', 'keyword' => 'quyt-thai'],
                            ['label' => 'Thanh trà', 'keyword' => 'thanh-tra'],
                        ],
                        'gio-qua-va-set-qua' => [
                            ['label' => 'Set quà 01', 'keyword' => 'MS06'],
                            ['label' => 'Set quà 02', 'keyword' => 'MS07'],
                            ['label' => 'Set quà 03', 'keyword' => 'MS08'],
                            ['label' => 'Set quà 04', 'keyword' => 'MS09'],
                            ['label' => 'Giỏ quà H31', 'keyword' => 'H31'],
                        ],
                    ];

                    $preferredChildItemsByRoot = [
                        'trai-cay-viet-nam' => [
                            ['slug' => 'xoai', 'label' => 'Xoài'],
                            ['slug' => 'cam', 'label' => 'Cam'],
                            ['slug' => 'dua', 'label' => 'Dưa'],
                            ['slug' => 'chuoi', 'label' => 'Chuối'],
                        ],
                        'trai-cay-nhap-khau' => [
                            ['slug' => 'nho-khong-hat', 'label' => 'Nho'],
                            ['slug' => 'tao', 'label' => 'Táo'],
                            ['slug' => 'cherry-my', 'label' => 'Cherry'],
                            ['slug' => 'kiwi-nhap-khau', 'label' => 'Kiwi'],
                            ['slug' => 'le-nhap-khau', 'label' => 'Lê'],
                        ],
                    ];

                    $fallbackProductsByCategory = Product::query()
                        ->select(['category_id', 'name', 'slug'])
                        ->where('is_active', true)
                        ->whereIn('category_id', $rootCategoryIds)
                        ->orderBy('name')
                        ->get()
                        ->groupBy('category_id');

                    $megaCategories->each(function (Category $category) use ($fallbackProductsByCategory, $presetFallbackMenuBySlug, $preferredChildItemsByRoot) {
                        $children = $category->children
                            ->filter(function (Category $childCategory) {
                                return (int) ($childCategory->active_products_count ?? 0) > 0;
                            })
                            ->values();

                        $preferredChildItems = collect($preferredChildItemsByRoot[$category->slug] ?? [])
                            ->map(function (array $preferredItem) use ($children) {
                                $matchedChild = $children->first(function (Category $childCategory) use ($preferredItem) {
                                    return (string) $childCategory->slug === (string) ($preferredItem['slug'] ?? '');
                                });

                                if (!$matchedChild) {
                                    return null;
                                }

                                return [
                                    'label' => (string) ($preferredItem['label'] ?? $matchedChild->name),
                                    'url' => route('categories.show', $matchedChild->slug),
                                ];
                            })
                            ->filter()
                            ->values();

                        $menuItems = ($preferredChildItems->isNotEmpty() ? $preferredChildItems : $children)
                            ->take(5)
                            ->map(function ($childCategory) {
                                if (is_array($childCategory)) {
                                    return $childCategory;
                                }

                                return [
                                    'label' => $childCategory->name,
                                    'url' => route('categories.show', $childCategory->slug),
                                ];
                            })
                            ->values();

                        $presetMenuItems = collect($presetFallbackMenuBySlug[$category->slug] ?? [])
                            ->map(function (array $preset) use ($category) {
                                $keyword = trim((string) ($preset['keyword'] ?? ''));
                                $tag = $keyword !== '' ? $keyword : trim((string) ($preset['label'] ?? ''));

                                return [
                                    'label' => $preset['label'] ?? '',
                                    'url' => $tag !== ''
                                        ? route('categories.show.tag', ['slug' => $category->slug, 'tag' => $tag])
                                        : route('categories.show', $category->slug),
                                ];
                            })
                            ->filter(function (array $item) {
                                return !empty($item['label']);
                            })
                            ->values();

                        // Keep curated short menu for Thai and gift roots like the reference layout.
                        if ($presetMenuItems->isNotEmpty()) {
                            $menuItems = $presetMenuItems;
                        }

                        if ($menuItems->isEmpty()) {
                            $menuItems = ($fallbackProductsByCategory->get($category->id) ?? collect())
                                ->take(5)
                                ->map(function (Product $product) use ($category) {
                                    $shortLabel = trim((string) preg_replace('/\s*-\s*.*$/u', '', (string) $product->name));
                                    $tag = $shortLabel !== '' ? $shortLabel : $product->name;

                                    return [
                                        'label' => $tag,
                                        'url' => route('categories.show.tag', ['slug' => $category->slug, 'tag' => $tag]),
                                    ];
                                })
                                ->values();
                        }

                        $category->setAttribute('mega_items', $menuItems);
                    });
                }
            }

            $view->with('sections', $sections);
            $view->with('megaCategories', $megaCategories);
        });
    }
}
