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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'view users']);

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'Super-Admin']);
        $role1->givePermissionTo('create users');
        $role1->givePermissionTo('update users');
        $role1->givePermissionTo('delete users');
        $role1->givePermissionTo('view users');

        $role2 = Role::create(['name' => 'Admin']);
        $role2->givePermissionTo('create users');
        $role2->givePermissionTo('update users');

        $role3 = Role::create(['name' => 'Manager']);
        $role3->givePermissionTo('create users');
        $role3->givePermissionTo('view users');
    }
}
