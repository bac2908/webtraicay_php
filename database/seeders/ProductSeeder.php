<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = Product::query()->count();

        if ($count === 0) {
            $this->command?->warn('Products table is empty. No static JSON seeding is performed.');
            return;
        }

        $this->command?->info("Products already available in MySQL: {$count}. Skipped static seeding.");
    }
}
