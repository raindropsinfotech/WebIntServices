<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //create permissions
        Permission::create(['name' => 'view products']);
        Permission::create(['name' => 'manage products']);
        Permission::create(['name' => 'view orders']);
        Permission::create(['name' => 'manage orders']);
        Permission::create(['name' => 'view orderItems']);
        Permission::create(['name' => 'manage orderItems']);
        Permission::create(['name' => 'view externalProducts']);
        Permission::create(['name' => 'manage externalProducts']);
        Permission::create(['name' => 'view notifications']);
        Permission::create(['name' => 'manage notifications']);

        //create roles

        $adminRole = Role::where('name', 'admin')->first();

        // If the admin role doesn't exist, create it
        if (!$adminRole) {
            $adminRole = Role::create(['name' => 'admin']);
            // Optionally, you can assign permissions to the admin role here
        }



        // $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());


        $adminUser = User::where('name', 'Admin User')->first();

        if (!$adminUser) {

            // Create an admin user
            $adminUser = User::create([
                'name' => 'Admin User',
                'email' => 'admin@intservicesllc.com',
                'password' => bcrypt('@Allah$786#'),
            ]);
        }
        // Assign admin role to the user
        // $adminRole = Role::findByName('admin');
        $adminUser->assignRole($adminRole);




        // to seed the data run: php artisan db:seed --class=RoleAndPermissionsSeeder
    }
}
