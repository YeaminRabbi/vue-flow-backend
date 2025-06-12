<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'string|required',
            'password' => 'required',
        ]);


        // Find the user by email and add the store in the user table
        $user = User::where('email', $request->email)->first();

        // Check if user exists, password matches, and the user has the 'user' or 'admin' role
        if (!$user || !Hash::check($request->password, $user->password)) {
            $message = !$user || !Hash::check($request->password, $user->password) ? 'Invalid email or password' : 'Unauthorized. Only authorized users can log in.';

            $statusCode = !$user || !Hash::check($request->password, $user->password) ? 401 : 403;

            return response()->json(
                [
                    'status' => $statusCode,
                    'message' => $message,
                ],
            );
        }

        // Generate a Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return the token and user details
        return response()->json([
            'status' => 200,
            'data' => [
                'token_type' => 'Bearer',
                'access_token' => $token,
                'user' => new UserResource($user),
            ],
        ]);
    }

    public function register(Request $request)
    {   

        $request->validate([
            'name' => 'required|string|max:225',
            'email' => 'required|string|email|unique:users,email', // Check if email already exists
            'password' => 'required|min:8',
            'confirm_password' => 'required|min:8|same:password',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $roleName = $request->input('role', 'admin');
        $role = Role::firstOrCreate(['name' => $roleName]);
        $user->assignRole($role->name);

        return response()->json([
            'status' => 200,
            'data' => [
                'user' => new UserResource($user),
            ],
        ]);
    }
   
}
