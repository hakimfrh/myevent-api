<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\VerificationEmail;
use Illuminate\Support\Facades\Mail;

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

        if (strlen($request->password) <= 6) {
            return response()->json(['message' => 'password to short'], 403);
        }

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'no_telp' => $request->no_telp,

            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat_perusahaan' => $request->alamat_perusahaan,
            'deskripsi_perusahaan' => $request->deskripsi_perusahaan,
        ]);
        if ($user) {
            return response()->json(['message' => 'ok'], 200);
        } else {
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
            if($user)
            return response()->json(['user' => $user], 200);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function getAllUser()
    {
        $user = User::get();
        return response()->json(['user' => $user], 200);
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
                return response()->json(['message' => 'ok'], 200);
            } else {
                return response()->json(['message' => 'password not match'], 403);
            }
        } else {
            return response()->json(['message' => 'user not found'], 406);
        }
        return response()->json(['message' => 'unknown error'], 406);
    }

    public function continueGoogle(Request $request)
    {
        $email = $request->email;
        $firebase_id = $request->firebase_id;

        if (!str_contains($email, '@')) {
            return response()->json(['message' => 'Email not valid'], 403);
        }
        if (empty($firebase_id)) {
            return response()->json(['message' => 'id not valid'], 403);
        }
        $user = User::where('email', $email)->first();
        if ($user) {
            if (empty($user->firebase_id)) {
                $user->firebase_id = $firebase_id;
                $user->save();
                return response()->json(['user' => $user], 200);
            } elseif ($user->firebase_id == $firebase_id) {
                return response()->json(['user' => $user], 200);
            } else {
                return response()->json(['message' => 'firebase id not match', 'code' => 403]);
            }
        } else {
            return response()->json(['message' => 'user not found'], 403);
        }
    }

    public function sendCode(Request $requests)
    {
        $verificationCode = rand(100000, 999999);
        $address = $requests->email;

        // Create a new instance of VerificationEmail and pass the verification code to it
        $email = new VerificationEmail($verificationCode);

        // Send the email using Laravel's Mail facade
        Mail::to($address)->send($email);
        return response()->json(['message' => 'ok'], 200);
    }
}
