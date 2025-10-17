<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions based on ACTUAL existing routes only
        $permissions = [
            // Dashboard (existing route)
            'access dashboard',
            
            // Profile (existing routes)
            'access profile',
            'edit profile',
            'delete profile',
            
            // Buttons (existing routes)
            'access buttons',
            
            // Role Management (existing routes)
            'manage roles',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'update role permissions',
            
            // User Management (existing routes)
            'manage users',
            'view users',
            'create users',
            'edit users',
            'delete users',
            'update user roles',

            // Permission Management (new routes)
            'manage permissions',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Super Admin - All permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - Most permissions
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo([
            'access dashboard', 'access profile', 'edit profile', 'access buttons',
            'manage roles', 'view roles', 'create roles', 'edit roles', 'delete roles', 'update role permissions',
            'manage users', 'view users', 'create users', 'edit users', 'delete users', 'update user roles',
            'manage permissions', 'view permissions', 'create permissions', 'edit permissions', 'delete permissions'
        ]);

        // Leader - Management permissions
        $leader = Role::firstOrCreate(['name' => 'Leader']);
        $leader->givePermissionTo([
            'access dashboard', 'access profile', 'edit profile',
            'manage users', 'view users', 'create users', 'edit users', 'update user roles'
        ]);

        // User - Basic permissions
        $user = Role::firstOrCreate(['name' => 'User']);
        $user->givePermissionTo([
            'access dashboard', 'access profile', 'edit profile'
        ]);
    }
}
