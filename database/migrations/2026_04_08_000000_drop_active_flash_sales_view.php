<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DropActiveFlashSalesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop all broken views that reference non-existent tables
        DB::statement('DROP VIEW IF EXISTS `active_promotions`');
        DB::statement('DROP VIEW IF EXISTS `safe_user_view`');
        DB::statement('DROP VIEW IF EXISTS `valid_coupons`');
        DB::statement('DROP VIEW IF EXISTS `active_flash_sales`');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Recreate the view if needed (currently we don't track promotions in schema)
        // If you need to restore this view, add the CREATE VIEW statement here
    }
}
