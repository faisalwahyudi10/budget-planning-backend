<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    public function login(Request $request)
    {
    
        try {
            // Validate Request
            $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            // Find user by username
            $credentials = request(['username', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error('Unauthorized', 401);
            }

            $user = User::where('username', $request->username)->first();
            if (!Hash::check($request->password, $user->password)) {
                throw new Exception('Invalid password');
            }

            // Generete token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // Token response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Login success');

        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage());
        }

    }

    public function logout(Request $request)
    {
        // Revoke token
        $token = $request->user()->currentAccessToken()->delete();

        // Return Token
        return ResponseFormatter::success($token, 'Logout success');
    }

    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 10);
        $role = $request->input('role');
        $status = $request->input('status', false);
        $withProgram = $request->input('with_programs', false);
        // Get user
        $userQuery = User::with('employee');

        if ($id) {
            $user = $userQuery->with(['employee', 'programs'])->find($id);

            if ($user) {
                return ResponseFormatter::success($user, 'User found');
            }

            return ResponseFormatter::error('User not found', 404);
        }

        $users = $userQuery;

        if ($role) {
            $users->where('role', $role);
        }
        
        if ($status) {
            $users->where('status', $status);
        }

        if ($withProgram) {
            $users->with('programs');
        }

        // Return response
        return ResponseFormatter::success($users->paginate($limit), 'Fetch success');
    }

    public function register(CreateUserRequest $request)
    {
        try {
            // Create user
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'employee_id' => $request->employee_id,
            ]);

            if (!$user) {
                throw new Exception('User not created');
            }

            //Return response
            return ResponseFormatter::success($user, 'Register success');

        } catch (Exception $e) {
            // return error response
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateUserRequest $request, $id)
    {
        try {
            // Get user
            $user = User::find($id);
            
            // Check if user exists
            if (!$user) {
                throw new Exception('User not found');
            }

            if (!Hash::check($request->old_password, $user->password)) {
                throw new Exception("Old Password Doesn't match!");
            }

            // Update user
            $user->update([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'employee_id' => $request->employee_id,
            ]);
    
            return ResponseFormatter::success($user, 'User Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function updateStatus(UpdateUserStatusRequest $request, $id)
    {
        try {
            // Get user
            $user = User::find($id);
            
            // Check if user exists
            if (!$user) {
                throw new Exception('User not found');
            }

            // Update user
            $user->update([
                'status' => $request->status,
            ]);
    
            return ResponseFormatter::success($user, 'User Status Updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
