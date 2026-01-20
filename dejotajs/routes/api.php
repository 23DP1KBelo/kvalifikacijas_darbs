<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\AppUserController;
use App\Http\Controllers\API\DanceGroupController;
use App\Http\Controllers\API\AgeGroupController;

Route::apiResource('posts', PostController::class);

Route::get('/posts/filter/public', [PostController::class, 'getPublic']);

Route::get('/posts/filter/private', [PostController::class, 'getPrivate']);

Route::apiResource('appUsers', AppUserController::class);

Route::apiResource('danceGroups', DanceGroupController::class);

Route::apiResource('ageGroups', AgeGroupController::class);

