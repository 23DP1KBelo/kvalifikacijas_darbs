<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\RegistrationController;

Route::post('/login', Login::class);
Route::post('/register',RegistrationController::class . '@register');

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

