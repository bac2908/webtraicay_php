<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixDataConsistencyIssues extends Migration
{
    /**
     * Run the migrations - Fix all data consistency issues
     *
     * @return void
     */
    public function up()
    {
        // 1. FIX: Products with price <= 0 (230 products)
        // Delete invalid products or set reasonable prices
        // For now, we'll delete products with price <= 0 since they can't be sold
        DB::statement('DELETE FROM product_images WHERE product_id IN (SELECT id FROM products WHERE price <= 0 OR price IS NULL)');
        DB::statement('DELETE FROM products WHERE price <= 0 OR price IS NULL');

        // 2. FIX: Products with NULL category_id (19 products)
        // Assign them to "Trái Cây Việt Nam" (category_id = 1) as default
        DB::statement('UPDATE products SET category_id = 1 WHERE category_id IS NULL');

        // 3. FIX: Expired coupons still marked as active
        // Deactivate coupons that have ends_at in the past
        DB::statement("UPDATE coupons SET is_active = 0 WHERE ends_at IS NOT NULL AND ends_at < NOW()");

        // 4. FIX: Sale price > regular price (illogical)
        // Set sale_price to NULL if it's greater than or equal to price
        DB::statement('UPDATE products SET sale_price = NULL WHERE sale_price IS NOT NULL AND sale_price >= price');

        // Log the fixes
        echo "\n✅ Database consistency fixes completed:\n";
        echo "   - Deleted products with invalid prices (price <= 0)\n";
        echo "   - Assigned NULL category products to default category\n";
        echo "   - Deactivated expired coupons\n";
        echo "   - Fixed illogical sale_price values\n";
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        // Note: We can't reliably reverse deletions, so no down migration
        echo "⚠️  Cannot reverse - data deletions are permanent\n";
    }
}
