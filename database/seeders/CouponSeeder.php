<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use App\Models\CouponImage;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $couponCount = Coupon::query()->count();
        $imageCount = CouponImage::query()->count();

        if ($couponCount === 0) {
            $this->command?->warn('Coupons table is empty. No static array seeding is performed.');
            return;
        }

        $this->command?->info("Coupons already available in MySQL: {$couponCount}, images: {$imageCount}. Skipped static seeding.");
    }
}
