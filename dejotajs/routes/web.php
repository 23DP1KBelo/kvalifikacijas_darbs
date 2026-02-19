<?php

use App\Http\Controllers\API\AppUserController;
use App\Http\Controllers\Auth\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\RegistrationController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\DanceGroupMemberController;
use App\Http\Controllers\API\DanceGroupController;

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

    Route::post('/api/danceGroups', [DanceGroupController::class, 'store']);
    Route::get('/api/danceGroups', [DanceGroupController::class, 'groupList']);
    Route::post('/api/members', [DanceGroupMemberController::class, 'leaderStore']);
    Route::get('/api/profile', ProfileController::class);
    Route::get('/api/my-posts/{danceGroupId}', [PostController::class, 'myGroupPosts']);
});

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/users', [AppUserController::class, 'index']);

    Route::get('/admin/danceGroups', [AdminController::class, 'showDanceGroups']);

    Route::post('/admin/danceGroups/approval', [AdminController::class, 'approveDanceGroup']);

    Route::post('/admin/danceGroups/decline', [AdminController::class, 'declineDanceGroup']);
});

Route::middleware(['auth', 'leader'])->group(function () {
    Route::get('/dancers/{group}', [DanceGroupMemberController::class, 'showDancers']);

    Route::post('/dancers/approval', [DanceGroupMemberController::class, 'approveMember']);

    Route::post('/dancers/decline', [DanceGroupMemberController::class, 'declineMember']);
    
    Route::get('/approval-leader/{group}', [DanceGroupMemberController::class, 'showLeaders']);

    Route::post('/approval-leader', [DanceGroupMemberController::class, 'approveMember']);

    Route::post('/decline-leader', [DanceGroupMemberController::class, 'declineMember']);
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

