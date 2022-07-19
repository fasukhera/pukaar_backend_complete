<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        // permissions
        Permission::create(['name' => 'be_admin']);
        Permission::create(['name' => 'have_client']);
        Permission::create(['name' => 'be_client']);

//        Permission::create(['name' => 'View User']);
//        Permission::create(['name' => 'Create User']);
//        Permission::create(['name' => 'Edit  User']);
//        Permission::create(['name' => 'Delete User']);


        // roles
        $role = Role::create(['name' => 'admin'])
            ->givePermissionTo('be_admin');
        $role = Role::create(['name' => 'therapist'])
            ->givePermissionTo(['have_client']);
        $role = Role::create(['name' => 'client'])
            ->givePermissionTo(['be_client']);
    }
}
