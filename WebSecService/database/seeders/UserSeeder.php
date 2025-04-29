<?php
namespace Database\Seeders; 
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Users to seed
        $users = [
            ['name' => 'admin1', 'email' => 'admin1@email.com', 'role' => 'Admin'],
            ['name' => 'admin2', 'email' => 'admin2@email.com', 'role' => 'Admin'],
            ['name' => 'customer1', 'email' => 'customer1@email.com', 'role' => 'Customer'],
            ['name' => 'customer2', 'email' => 'customer2@email.com', 'role' => 'Customer'],
            ['name' => 'employee1', 'email' => 'employee1@email.com', 'role' => 'Employee'],
            ['name' => 'employee2', 'email' => 'employee2@email.com', 'role' => 'Employee'],
        ];

        // Loop through users and create them
        foreach ($users as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email'],
            ], [
                'name' => $userData['name'],
                'password' => Hash::make($userData['name'] . '$A1'), // Password = username + $A1
            ]);

            

            $role = Role::where('name',$userData['role'])->where('guard_name', 'web')->first();
            // Assign role
            $user->assignRole($role);

            
        }
        echo "Users seeded successfully!\n";
    }
}