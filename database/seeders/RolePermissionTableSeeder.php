<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionTableSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        //app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'can make sales', 'guard_name' => 'admin', 'company_id' => 1]);
        Permission::create(['name' => 'can give discounts', 'guard_name' => 'admin', 'company_id' => 1]);
        Permission::create(['name' => 'can add stock-in', 'guard_name' => 'admin', 'company_id' => 1]);
        Permission::create(['name' => 'can add new products', 'guard_name' => 'admin', 'company_id' => 1]);
        Permission::create(['name' => 'can add expenses', 'guard_name' => 'admin', 'company_id' => 1]);
        Permission::create(['name' => 'can view & manage customers', 'guard_name' => 'admin', 'company_id' => 1]);
        Permission::create(['name' => 'can view & manage suppliers', 'guard_name' => 'admin', 'company_id' => 1]);
        Permission::create(['name' => 'can view stock balance', 'guard_name' => 'admin', 'company_id' => 1]);
        Permission::create(['name' => 'can view other shops stock balance', 'guard_name' => 'admin', 'company_id' => 1]);
        Permission::create(['name' => 'can count and update stock balance', 'guard_name' => 'admin', 'company_id' => 1]);
        Permission::create(['name' => 'can edit daily entries', 'guard_name' => 'admin', 'company_id' => 1]);
        Permission::create(['name' => 'can delete daily entries', 'guard_name' => 'admin', 'company_id' => 1]);
        Permission::create(['name' => 'can back date entries', 'guard_name' => 'admin', 'company_id' => 1]);

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'Super-Admin', 'guard_name' => 'admin', 'company_id' => 1]);
        $role1->givePermissionTo('can make sales');
        $role1->givePermissionTo('can give discounts');
        $role1->givePermissionTo('can add stock-in');
        $role1->givePermissionTo('can add new products');
        $role1->givePermissionTo('can add expenses');
        $role1->givePermissionTo('can view & manage suppliers');
        $role1->givePermissionTo('can view & manage customers');
        $role1->givePermissionTo('can view stock balance');
        $role1->givePermissionTo('can view other shops stock balance');
        $role1->givePermissionTo('can count and update stock balance');
        $role1->givePermissionTo('can delete daily entries');
        $role1->givePermissionTo('can edit daily entries');
        $role1->givePermissionTo('can back date entries');

        $role2 = Role::create(['name' => 'Admin', 'guard_name' => 'admin', 'company_id' => 1]);
        $role2->givePermissionTo('can make sales');
        $role2->givePermissionTo('can view & manage suppliers');
        $role2->givePermissionTo('can view & manage customers');
        $role2->givePermissionTo('can edit daily entries');
        $role1->givePermissionTo('can view stock balance');
        $role1->givePermissionTo('can add new products');
        $role1->givePermissionTo('can count and update stock balance');
        $role1->givePermissionTo('can add stock-in');

        $role3 = Role::create(['name' => 'Manager', 'guard_name' => 'admin', 'company_id' => 1]);
        $role3->givePermissionTo('can make sales');
        $role3->givePermissionTo('can edit daily entries');
    }
}
