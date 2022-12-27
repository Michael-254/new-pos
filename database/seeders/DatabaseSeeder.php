<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolePermissionTableSeeder::class);
        $this->call(AdminTableSeeder::class);
        $this->call(BusinessSettingTableSeeder::class);
        $this->call(CustomerLoginTableSeeder::class);
        $this->call(CustomerTableSeeder::class);
        $this->call(ProductTableSeeder::class);
        $this->call(MpesaCredentialTableSeeder::class);
        $this->call(CurrencyTableSeeder::class);
    }
}
