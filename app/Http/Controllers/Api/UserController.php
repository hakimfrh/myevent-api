<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        /*
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8',
        ],[
            'email.unique' => 'The email address is already in use.',
            'username.unique' => 'The username is already taken.',
            'password.min' => 'The password must be at least 8 characters.',
        ]);
*/

        // Check if the username already exists
        $usernameExists = User::where('username', $request->username)->exists();
        if ($usernameExists) {
            return response()->json(['message' => 'Username already exists'], 403);
        }

        // Check if the email is valid and not already used
        if (!str_contains($request->email, '@')) {
            return response()->json(['message' => 'Email not valid'], 403);
        }

        $emailExists = User::where('email', $request->email)->exists();
        if ($emailExists) {
            return response()->json(['message' => 'Email already used'], 403);
        }

        if (strlen($request->password) <= 8) {
            return response()->json(['message' => 'password to short'], 403);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,

            'business_name' => $request->business_name,
            'business_location' => $request->business_location,
            'business_description' => $request->business_description,
        ]);
        if ($user) {
            return response()->json(['message' => 'ok'], 201);
        }else{
            return response()->json(['message' => 'unknown eror while creating user'], 406);
        }
    }

    public function login(Request $request)
    {
        if (str_contains($request->login, '@')) {
            $credentials = [
                'email' => $request['login'],
                'password' => $request['password'],
            ];
        } else {
            $credentials = [
                'username' => $request['login'],
                'password' => $request['password'],
            ];
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return response()->json(['user' => $user], 200);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function updatePassword(Request $request)
    {
        $id = $request->id;
        $currentPassword = $request->current_password;
        $newPassword = $request->new_password;

        if (strlen($newPassword) <= 8) {
            return response()->json(['message' => 'password to short'], 403);
        }

        $user = User::find($id);
        if ($user) {
            if (Hash::check($currentPassword, $user->password)) {
                $user->password = bcrypt($newPassword);
                $user->save();
                return response()->json(['message' => 'ok'], 201);
            } else {
                return response()->json(['message' => 'password not match'], 403);
            }
        } else {
            return response()->json(['message' => 'user not found'], 406);
        }
        return response()->json(['message' => 'unknown error'], 406);
    }
}
