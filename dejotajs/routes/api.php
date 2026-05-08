<?php

use App\Http\Controllers\API\AdmissionController;
use App\Http\Controllers\API\AgeGroupController;
use App\Http\Controllers\API\AppUserController;
use App\Http\Controllers\API\DanceGroupController;
use App\Http\Controllers\API\DanceGroupMemberController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\Auth\AdminController;
use App\Http\Controllers\Auth\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Resources\DanceGroupMemberResource;


Route::apiResource('posts', PostController::class);

Route::get('/posts/filter/public', [PostController::class, 'getPublic']);

Route::get('/posts/filter/private', [PostController::class, 'getPrivate']);

Route::apiResource('users', AppUserController::class);

Route::apiResource('danceGroups', DanceGroupController::class);

Route::apiResource('ageGroups', AgeGroupController::class);

Route::apiResource('members', DanceGroupMemberController::class);

Route::apiResource('admissions', AdmissionController::class);

Route::apiResource('events', EventController::class);


Route::post('/login', Login::class);
Route::post('/register', [RegistrationController::class, 'register']);

Route::get('/danceGroups-all', [DanceGroupController::class, 'getAllGroups']);
Route::get('/search-dance-groups', [DanceGroupController::class, 'search']);
Route::get('/filter-dance-groups', [DanceGroupController::class, 'filterGroups']);
Route::get('/sort-dance-groups/{sort}', [DanceGroupController::class, 'groupSorting']);
Route::get('/filter-options', [DanceGroupController::class, 'filterOptions']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/dance-group-info/{danceGroup}', [DanceGroupController::class, 'show']);
Route::get('/admission-age-groups', [AgeGroupController::class, 'getAdmissonAgeGroups']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    /** @var \App\Models\AppUser $user */
    $user = $request->user();

    return response()->json([
        'user_id' => $user->id,
        'user_model' => get_class($user),
        'members_count' => $user->danceGroupMembers()->count(),
        'dance_group_members' => $user->danceGroupMembers()->get(),
    ]);
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/danceGroups', [DanceGroupController::class, 'store']);
    Route::get('/danceGroups', [DanceGroupController::class, 'groupList']);

    Route::post('/members', [DanceGroupMemberController::class, 'leaderStore']);
    Route::post('/members/join', [DanceGroupMemberController::class, 'store']);

    Route::get('/profile', [ProfileController::class, 'index']);
    Route::get('/my-posts/{danceGroupId}', [PostController::class, 'myGroupPosts']);
    Route::get('/groupListApproved', [DanceGroupController::class, 'groupListApprovedMember']);

    Route::get('/ageGroups', [AgeGroupController::class, 'index']);

    Route::post('/events/create', [EventController::class, 'store']);
    Route::post('/{event}/attach', [EventController::class, 'attachAgeGroups']);

    Route::get('/leader-groups', [DanceGroupController::class, 'leaderGroups']);

    Route::delete('/event/{event}', [EventController::class, 'destroy']);
    Route::put('/event/{event}', [EventController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'leader'])->group(function () {

    Route::get('/dancers/{group}', [DanceGroupMemberController::class, 'showDancers']);

    Route::post('/dancers/approval', [DanceGroupMemberController::class, 'approveMember']);
    Route::post('/dancers/decline', [DanceGroupMemberController::class, 'declineMember']);

    Route::get('/approval-leader/{group}', [DanceGroupMemberController::class, 'showLeaders']);

    Route::post('/approval-leader', [DanceGroupMemberController::class, 'approveMember']);
    Route::post('/decline-leader', [DanceGroupMemberController::class, 'declineMember']);

    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    Route::post('/ageGroups/create', [AgeGroupController::class, 'store']);

    Route::put('/danceGroups/{id}', [DanceGroupController::class, 'update']);
    Route::delete('/danceGroups/{id}', [DanceGroupController::class, 'destroy']);

    Route::post('/admission/create', [AdmissionController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {

    Route::get('/admin/users', [AppUserController::class, 'index']);

    Route::get('/admin/danceGroups', [AdminController::class, 'showDanceGroups']);
    Route::post('/admin/danceGroups/approval', [AdminController::class, 'approveDanceGroup']);
    Route::post('/admin/danceGroups/decline', [AdminController::class, 'declineDanceGroup']);

    Route::delete('/posts/admin/{id}', [PostController::class, 'destroy']);
    Route::delete('/event/admin/{event}', [EventController::class, 'destroy']);

    Route::get('/admin/stats', [DanceGroupController::class, 'index']);
    Route::get('/admin/stats-dancers', [DanceGroupController::class, 'getAllGroups']);
});


