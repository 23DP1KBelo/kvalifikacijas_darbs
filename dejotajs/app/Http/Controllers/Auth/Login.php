<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;

class Login extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            /** @var \App\Models\AppUser $user */
            $user = Auth::user();

            return response()->json([
                'message' => 'Pieslēgšanās veiksmīga',
                'user' => $user
            ], 200);
        }

        return response()->json(['message' => 'Nepareizs e-pasts vai parole'], 401);
    }
}

