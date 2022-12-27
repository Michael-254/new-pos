<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = DB::table('admins')->insert([
            'id' => 1,
            'company_id' => 1,
            'f_name' => 'Master Admin',
            'l_name' => 'Wanguba',
            'email' => 'admin@admin.com',
            'password' => bcrypt(12345678),
            'remember_token' =>Str::random(10),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // $role = Role::find(1);
        // $user->assignRole($role);
    }
}
