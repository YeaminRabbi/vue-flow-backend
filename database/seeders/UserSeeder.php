<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Check if the 'user' role already exists, if not, create it
        $role = Role::firstOrCreate(['name' => 'user']);

        // Check if the user with email 'user@gmail.com' already exists
        $user = User::where('email', 'user@gmail.com')->first();

        if (!$user) {
            // Creating the user user if it does not already exist
            $user = new User;
            $user->name = 'user';
            $user->email = 'user@gmail.com';
            $user->password = Hash::make('123'); // Use a more secure password in production
            $user->email_verified_at = now(); // Set email verification date
            $user->save();

            // Assign the 'user' role to the user
            $user->assignRole($role);
        } else {
            // Optionally, update user details if the user exists
            $user->update([
                'name' => 'user',
                'password' => Hash::make('123'), // Update the password if necessary
            ]);

            // Ensure the user has the 'user' role
            if (!$user->hasRole('user')) {
                $user->assignRole($role);
            }
        }
    }
}
