<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
           
            'admin' => [
                // Dashboard and general permissions
                'view_dashboard',
               
            ],
            'user' => [
                // Dashboard and general permissions
                'view_dashboard',
            ],
        ];

        // Loop through each role
        foreach ($roles as $roleName => $rolePermissions) {
            // Check if the role already exists
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Assign permissions to the role
            foreach ($rolePermissions as $permissionName) {
                $permission = Permission::firstOrCreate(['name' => $permissionName]);
                $role->givePermissionTo($permission);
            }
        }
    }
}
