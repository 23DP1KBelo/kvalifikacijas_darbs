<?php

use App\Http\Controllers\API\AdmissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\AppUserController;
use App\Http\Controllers\API\DanceGroupController;
use App\Http\Controllers\API\AgeGroupController;
use App\Http\Controllers\API\DanceGroupMemberController;
use App\Http\Controllers\API\EventController;

Route::apiResource('posts', PostController::class);

Route::get('/posts/filter/public', [PostController::class, 'getPublic']);

Route::get('/posts/filter/private', [PostController::class, 'getPrivate']);

Route::apiResource('users', AppUserController::class);

Route::apiResource('danceGroups', DanceGroupController::class);

Route::apiResource('ageGroups', AgeGroupController::class);

Route::apiResource('members', DanceGroupMemberController::class);

Route::apiResource('admissions', AdmissionController::class);

Route::apiResource('events', EventController::class);



