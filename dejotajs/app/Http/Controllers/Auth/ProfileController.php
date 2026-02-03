<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DanceGroupMemberResource;

class ProfileController extends Controller
{
    public function __invoke()
    {
        /** @var \App\Models\AppUser $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        // Ielādē dance group members ar visām saistītajām tabulām
        $members = $user->danceGroupMembers()->with(['danceGroup', 'appUser', 'ageGroup'])->get();

        return DanceGroupMemberResource::collection($members);
    }
}


