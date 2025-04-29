<?php
namespace Database\Seeders; 
// filepath: c:\Users\ziadm\OneDrive\Desktop\websec-main\WebSecService\database\seeders\RoleSeeder.php
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;


class RoleSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $roles = [
            ['id' => 1, 'name' => 'Admin', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'Employee', 'guard_name' => 'web'],
            ['id' => 3, 'name' => 'Customer', 'guard_name' => 'web'],
        ];

  
 

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'id' => $role['id'],
                'name' => $role['name'],
                'guard_name' => $role['guard_name']
            ]);
        } 
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}