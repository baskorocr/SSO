<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::updateOrCreate(
            ['npk' => '00000001'],
            [
                'name' => 'Administrator',
                'email' => 'admin@sinta.dharmap.com',
                'password' => bcrypt('admin123'),
            ]
        );
        $admin->assignRole('admin');

        // Create Manager Users
        $manager1 = User::updateOrCreate(
            ['npk' => '00000002'],
            [
                'name' => 'Manager IT',
                'email' => 'manager.it@sinta.dharmap.com',
                'password' => bcrypt('manager123'),
            ]
        );
        $manager1->assignRole('manager');

        $manager2 = User::updateOrCreate(
            ['npk' => '00000003'],
            [
                'name' => 'Manager HR',
                'email' => 'manager.hr@sinta.dharmap.com',
                'password' => bcrypt('manager123'),
            ]
        );
        $manager2->assignRole('manager');

        // Create Regular Users
        $users = [
            ['npk' => '00000004', 'name' => 'John Doe', 'email' => 'john.doe@sinta.dharmap.com'],
            ['npk' => '00000005', 'name' => 'Jane Smith', 'email' => 'jane.smith@sinta.dharmap.com'],
            ['npk' => '00000006', 'name' => 'Bob Johnson', 'email' => 'bob.johnson@sinta.dharmap.com'],
            ['npk' => '00000007', 'name' => 'Alice Brown', 'email' => 'alice.brown@sinta.dharmap.com'],
            ['npk' => '00000008', 'name' => 'Charlie Wilson', 'email' => 'charlie.wilson@sinta.dharmap.com'],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['npk' => $userData['npk']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => bcrypt('user123'),
                ]
            );
            $user->assignRole('user');
        }
    }
}
