<?php

namespace App\Http\Controllers;

use App\Services\HomeService;

class HomeController extends Controller
{
    protected $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    public function index()
    {
        $categorySlugs = config('shop.home_categories', []);

        return view('home', [
            'topCategories' => $this->homeService->getTopCategories(),
            'coupons'       => $this->homeService->getActiveCoupons(6),
            'sections'      => $this->homeService->getHomeSections($categorySlugs),
        ]);
    }
}
