<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerLoginTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customer_logins')->insert([
            'id' => 1,
            'f_name' => 'Master Customer',
            'l_name' => 'Wanguba',
            'email' => 'admin@admin.com',
            'password' => bcrypt(12345678),
            'remember_token' =>Str::random(10),
            'created_at'=>now(),
            'updated_at'=>now()
        ]);
    }
}
