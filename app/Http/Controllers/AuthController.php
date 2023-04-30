<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    private function payload($data = [], $code = 200, $message = 'Success')
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
    }

    public function registration(Request $request)
    {
        $request->validate([
            'username' => ['required', 'unique:users', 'max:20', 'min:4'],
            'password' => ['confirmed', 'min:3', 'required', 'max:20']
        ]);

        $request->merge([
            'password' => bcrypt($request->password),
            'username' => strtolower(str_replace(' ', '', $request->username)),
        ]);
        $request->validate([
            'username' => ['required', 'unique:users', 'max:20', 'min:4'],
        ]);

        $user = User::create($request->except('password_confirmation'));

        return response()->json($this->payload($user, 200, 'Account Created'));
    }

    public function login(Request $request)
    {

        $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        $user = User::where('username', $request->username)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                return response()->json($this->payload([
                    'token' => $user->createToken($request->username)->plainTextToken
                ]));
            }
        }
        throw ValidationException::withMessages([
            'username' => 'Credentials are invalid or account never really existed'
        ]);
    }
}
