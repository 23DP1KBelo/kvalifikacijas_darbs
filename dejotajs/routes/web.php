<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return response()->json(['message' => 'Logged out']);
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');



