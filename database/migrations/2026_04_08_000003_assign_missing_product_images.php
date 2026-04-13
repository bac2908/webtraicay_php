<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AssignMissingProductImages extends Migration
{
    /**
     * Run the migrations - Assign images to products that are missing them
     *
     * @return void
     */
    public function up()
    {
        // Product ID 3: Vú sữa tím Mica - 1kg (Category: Trái Cây Việt Nam)
        DB::table('products')->where('id', 3)->update([
            'thumb' => 'images/images_traicayvn/vu_sua_tim_71a6600dbb5d44bd9ca5c286a62dc6ef_medium.jpg'
        ]);

        // Product ID 4: Sầu riêng Ri6 (nguyên trái) - kg (Category: Trái Cây Việt Nam)
        DB::table('products')->where('id', 4)->update([
            'thumb' => 'images/images_traicayvn/com_sau_rieng_ri6__2__3a18e9d2c6df4ad09e8d28ed7d6cb8fb_medium.png'
        ]);

        // Product ID 5: Táo Juliet Pháp Organic size 88 - kg (Category: Trái Cây Nhập Khẩu)
        DB::table('products')->where('id', 5)->update([
            'thumb' => 'images/images_traicaynhapkhau/tao-envy_my-cap__2__c55647893c1b4846838139b412831875_medium.jpg'
        ]);

        // Product ID 7: Việt quất Newzealand - hộp 125gr (Category: Trái Cây Nhập Khẩu)
        DB::table('products')->where('id', 7)->update([
            'thumb' => 'images/images_traicaynhapkhau/viet_quat_newzealand_18381a1218db46618efd9279a44ad3dd_medium.png'
        ]);

        // Product ID 8: Chôm chôm Thái - kg (Category: Trái Cây Thái Lan)
        DB::table('products')->where('id', 8)->update([
            'thumb' => 'images/images_traicaythailan/chom_chom_thai_a319bff72420464c86851be5a435c1c6_medium.png'
        ]);

        // Product ID 9: Thanh trà Thái Lan - kg (Category: Trái Cây Thái Lan)
        DB::table('products')->where('id', 9)->update([
            'thumb' => 'images/images_traicaythailan/thanh_tra_fbe44757213e49ea925aa570fc0863a3_medium.png'
        ]);

        // Product ID 10: Giỏ trái cây Kính lễ KL1350 (Category: Giỏ quà và Set quà)
        DB::table('products')->where('id', 10)->update([
            'thumb' => 'images/Gioquavasetqua/dsc_5485_copy_2405c85b8fad44a192388fd560ee67d9_medium.jpg'
        ]);
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        // Remove assigned images
        DB::table('products')->whereIn('id', [3, 4, 5, 7, 8, 9, 10])
            ->update(['thumb' => null]);
    }
}
