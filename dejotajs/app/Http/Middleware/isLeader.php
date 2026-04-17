<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsLeader
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Nav ielogojies
        if (!$user) {
            return response()->json(['message' => 'Neautorizēts'], 401);
        }

        // 2. Admin vienmēr drīkst
        if ($user->role === 'admin') {
            return $next($request);
        }

        // 3. Dabū group ID
        $group = $request->route('group');
        $groupId = $group instanceof \App\Models\DanceGroup
            ? $group->id
            : $request->input('dance_group_id');

        // 4. JA nav group → NEBLOĶĒ requestu
        if (!$groupId) {
            return $next($request);
        }

        // 5. Pārbauda vai user ir leader šajā grupā
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