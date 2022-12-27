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
        Permission::create(['name' => 'edit users', 'guard_name' => 'admin']);
        Permission::create(['name' => 'delete users', 'guard_name' => 'admin']);
        Permission::create(['name' => 'create users', 'guard_name' => 'admin']);
        Permission::create(['name' => 'list users', 'guard_name' => 'admin']);

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'Super-Admin', 'guard_name' => 'admin']);
        $role1->givePermissionTo('create users');
        $role1->givePermissionTo('edit users');
        $role1->givePermissionTo('delete users');
        $role1->givePermissionTo('list users');

        $role2 = Role::create(['name' => 'Admin', 'guard_name' => 'admin']);
        $role2->givePermissionTo('create users');
        $role2->givePermissionTo('edit users');
        $role2->givePermissionTo('list users');

        $role3 = Role::create(['name' => 'Manager', 'guard_name' => 'admin']);
        $role3->givePermissionTo('create users');
        $role3->givePermissionTo('list users');
    }
}
