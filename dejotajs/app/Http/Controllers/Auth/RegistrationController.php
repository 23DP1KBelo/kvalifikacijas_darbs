<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AppUserRequest;
use App\Models\AppUser;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function register(AppUserRequest $request)
    {
        $validated = $request->validated();

        $user = AppUser::create([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
        ]);
        return response()->json(['message' => 'Reģistrācija veiksmīga', 'user' => $user], 201);
    }
}
