<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConstraintsAndIndexes extends Migration
{
    /**
     * Add missing unique constraints and performance indexes
     *
     * @return void
     */
    public function up()
    {
        // 1. Add unique constraint to carts (one cart per user)
        Schema::table('carts', function (Blueprint $table) {
            $table->unique(['user_id'], 'unique_user_cart');
        });

        // 2. Add unique constraint to cart_items (prevent duplicate products in cart)
        Schema::table('cart_items', function (Blueprint $table) {
            $table->unique(['cart_id', 'product_id'], 'unique_cart_product');
        });

        // 3. Add performance indexes to products table
        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('is_active');
            $table->index('created_at');
        });

        // 4. Add performance indexes to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });

        // 5. Add performance indexes to coupons table
        Schema::table('coupons', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('code');
        });

        // 6. Add soft deletes to important tables (for data recovery/audit)
        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Note: Orders should NEVER be deleted, so we don't add soft deletes
        // Instead, change status to 'cancelled' if needed
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropUnique('unique_user_cart');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropUnique('unique_cart_product');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_category_id_index');
            $table->dropIndex('products_is_active_index');
            $table->dropIndex('products_created_at_index');
            $table->dropSoftDeletes();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_id_index');
            $table->dropIndex('orders_status_index');
            $table->dropIndex('orders_created_at_index');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex('coupons_is_active_index');
            $table->dropIndex('coupons_code_index');
            $table->dropSoftDeletes();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
