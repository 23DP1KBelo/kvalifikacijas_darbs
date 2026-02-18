<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsLeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
public function handle(Request $request, Closure $next): Response
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'Neautorizēts'], 401);
    }

    // Iegūst grupas id no route (GET) vai request (POST)
    $group = $request->route('group');
    $groupId = $group instanceof \App\Models\DanceGroup ? $group->id : $request->input('dance_group_id');

    if (!$groupId) {
        return response()->json(['message' => 'Nav norādīta grupa'], 400);
    }

    // Pārbauda, vai lietotājs šajā grupā ir leader
    $isLeader = $user->danceGroupMembers()
                     ->where('dance_group_id', $groupId)
                     ->where('role', 'leader')
                     ->exists();

    if (!$isLeader) {
        return response()->json(['message' => 'Jums nav piekļuves šim kolektīvam'], 403);
    }

    return $next($request);
}


}