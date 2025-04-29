<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
         // Define the permissions
        $permissions = [
            ['id' => 1,'name' => 'add_products', 'display_name' => 'Add Products', 'guard_name' => 'web'],
            ['id' => 2,'name' => 'edit_products', 'display_name' => 'Edit Products', 'guard_name' => 'web'],
            ['id' => 3,'name' => 'delete_products', 'display_name' => 'Delete Products', 'guard_name' => 'web'],
            ['id' => 4,'name' => 'show_users', 'display_name' => 'Show Users', 'guard_name' => 'web'],
            ['id' => 5,'name' => 'edit_users', 'display_name' => 'Edit Users', 'guard_name' => 'web'],
            ['id' => 6,'name' => 'delete_users', 'display_name' => 'Delete Users', 'guard_name' => 'web'],
            ['id' => 7,'name' => 'admin_users', 'display_name' => 'Admin Users', 'guard_name' => 'web'],
            ['id' => 8,'name' => 'add_users', 'display_name' => 'Add Users', 'guard_name' => 'web'],
            ['id' => 9,'name' => 'edit_user_credit', 'display_name' => 'Edit User Credit', 'guard_name' => 'web'],
            ['id' => 19,'name' => 'reset_credit', 'display_name' => 'reset credit', 'guard_name' => 'web'],
            ['id' => 10,'name' => 'purchase', 'display_name' => 'Purchase', 'guard_name' => 'web'],
            ['id' => 11,'name' => 'show_purchased', 'display_name' => 'Show Purchased', 'guard_name' => 'web'],
            ['id' => 12,'name' => 'show_customers', 'display_name' => 'Show Customers', 'guard_name' => 'web'],
            ['id' => 13,'name' => 'show_products', 'display_name' => 'Show Products', 'guard_name' => 'web'],

            
        ];
 
        // Insert permissions
        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'id' => $perm['id'],
                'name' => $perm['name'],
                'guard_name' => $perm['guard_name']
            ], [
                'display_name' => $perm['display_name']
            ]);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Assign all permissions to admin role
        $adminRole = Role::firstOrCreate(attributes: ['name' => 'Admin', 'guard_name' => 'web']);
        $adminPermissions = Permission::all()->reject(function ($permission) {
            return in_array($permission->name, ['purchase','show_purchased']);
        });

        $adminRole->givePermissionTo($adminPermissions);

        
        // Assign customer permissions
        $customerRole = Role::firstOrCreate(['name' => 'Customer', 'guard_name' => 'web']);
        $customerPermissions = Permission::whereIn('name', ['show_products','purchase', 'show_purchased'])->get();
        $customerRole->syncPermissions([$customerPermissions]);


         // Assign employee permissions
        $employeeRole = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
        $employeePermissions = Permission::whereIn('name', ['show_products','add_products','edit_products','delete_products','show_customers', 'edit_user_credit'])->get();
        $employeeRole->syncPermissions([$employeePermissions]);
    }
}