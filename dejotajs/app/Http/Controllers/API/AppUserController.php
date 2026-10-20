<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppUser;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AppUserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\PasswordChangeRequest;
use App\Http\Requests\AppUserUpdateRequest;

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

    // Update password
    public function updatePassword(PasswordChangeRequest $request) {
         /** @var AppUser $user */

        $user = Auth::user();

        if(!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Pašreizējā parole neatbilst'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Parole veiksmīgi nomainīta.'
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AppUserUpdateRequest $request){

        /** @var AppUser $user */

        $user = Auth::user();
        $validated = $request->validated();

        $user->update($validated);

        return new AppUserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AppUser $user)
    {
         $user->delete();

        return response()->json([
            'message' => 'Lietotājs veiksmīgi izdzēsts.',
        ]);
    }
}
