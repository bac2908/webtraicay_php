<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\HomeService;
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
                            $query->where('is_active', true)->orderBy('sort_order');
                        }
                    ])
                    ->orderByRaw($slugOrderSql)
                    ->get();
            }

            $view->with('sections', $sections);
            $view->with('megaCategories', $megaCategories);
        });
    }
}
