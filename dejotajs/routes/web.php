<?php

use App\Http\Controllers\API\AdmissionController;
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
use App\Http\Controllers\API\AgeGroupController;
use App\Http\Controllers\API\EventController;

Route::post('/login', Login::class);
Route::post('/register',RegistrationController::class . '@register');
Route::get('/danceGroups-all', [DanceGroupController::class, 'getAllGroups']);
Route::get('/api/dance-group-info/{danceGroup}', [DanceGroupController::class, 'show']);
Route::get('/search-dance-groups', [DanceGroupController::class, 'search']);
Route::get('/sort-dance-groups/{sort}', [DanceGroupController::class, 'groupSorting']);
Route::get('/filter-dance-groups', [DanceGroupController::class, 'filterGroups']);
Route::get('/filter-options', [DanceGroupController::class, 'filterOptions']);
Route::get('/admission-age-groups', [AgeGroupController::class, 'getAdmissonAgeGroups']);
Route::get('/events', [EventController::class, 'index']);

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
    Route::get('/api/profile', [ProfileController::class, 'index']);
    Route::get('/api/my-posts/{danceGroupId}', [PostController::class, 'myGroupPosts']);
    Route::get('/api/groupListApproved', [DanceGroupController::class, 'groupListApprovedMember']);
    Route::post('/api/members/join', [DanceGroupMemberController::class, 'store']);
    Route::get('/api/ageGroups', [AgeGroupController::class, 'index']);
    Route::post('/api/events/create', [EventController::class, 'store']);
    Route::post('/api/{event}/attach', [EventController::class, 'attachAgeGroups']);
    Route::get('/api/leader-groups', [DanceGroupController::class, 'leaderGroups']);
    Route::delete('/event/{event}', [EventController::class, 'destroy']);
    Route::put('/event/{event}', [EventController::class, 'update']);
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

    Route::post('/posts', [PostController::class, 'store']);

    Route::post('/api/ageGroups/create', [AgeGroupController::class, 'store']);

    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    Route::put('/posts/{id}', [PostController::class, 'update']);

    Route::delete('/danceGroups/{id}', [DanceGroupController::class, 'destroy']);

    Route::put('/danceGroups/{id}', [DanceGroupController::class, 'update']);

    Route::post('/api/admission/create', [AdmissionController::class, 'store']);
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

