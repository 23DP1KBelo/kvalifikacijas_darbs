<?php

use App\Http\Controllers\API\AppUserController;
use App\Http\Controllers\Auth\AdminController;
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

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/users', [AppUserController::class, 'index']);

    Route::get('/admin/danceGroups', [AdminController::class, 'showDanceGroups']);

    Route::post('/admin/danceGroups/approval', [AdminController::class, 'approveDanceGroup']);

    Route::post('/admin/danceGroups/decline', [AdminController::class, 'declineDanceGroup']);
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

