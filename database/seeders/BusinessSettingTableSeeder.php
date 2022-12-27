<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BusinessSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
            'id' => 1,
            'company_id' => 1,
            'shop_logo' => '2022-10-17-634c4a5c98a27.png',
            'pagination_limit' => '12',
            'currency' => 'KES',
            'shop_name' => 'AM tech Shop',
            'shop_address' => 'Nairobi',
            'shop_phone' => '0123456789',
            'app_minimum_version_ios' => '1.0',
            'shop_email' => 'shop@gmail.com',
            'footer_text' => 'Foooter text',
            'country' => 'KE',
            'stock_limit' => '10',
            'time_zone' => 'Africa/Nairobi',
            'vat_reg_no' => '0000',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
