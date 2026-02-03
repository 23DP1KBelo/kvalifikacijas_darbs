<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\RegistrationController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\ProfileController;

Route::post('/login', Login::class);
Route::post('/register',RegistrationController::class . '@register');

Route::middleware('web')->get('/user', function () {
    return response()->json([
        'logged_in' => Auth::check(),
        'user' => Auth::user()
    ]);
});

Route::middleware('auth')->group(function (){
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return response()->json(['message' => 'Logged out']);
    });

    Route::get('api/profile', ProfileController::class);
});



Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

