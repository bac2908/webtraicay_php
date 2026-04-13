<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = Category::query()->count();

        if ($count === 0) {
            $this->command?->warn('Categories table is empty. No static JSON seeding is performed.');
            return;
        }

        $this->command?->info("Categories already available in MySQL: {$count}. Skipped static seeding.");
    }
}
