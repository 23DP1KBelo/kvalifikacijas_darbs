<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppUser;
use App\Http\Resources\AppUserResource;

use App\Http\Requests\AppUserRequest;

class AppUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = AppUser::with('danceGroupMembers')->get();

        return AppUserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AppUserRequest $request)
    {
        $validated = $request->validated();

        $user = AppUser::create($validated);

        return (new AppUserResource($user))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AppUser $appUser)
    {
        return new AppUserResource($appUser);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AppUserRequest $request, AppUser $appUser)
    {
         $validated = $request->validated();

         $appUser->update($validated);

         return new AppUserResource($appUser);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppUser $appUser)
    {
        $appUser->delete();
        
        return new AppUserResource($appUser);
    }
}
