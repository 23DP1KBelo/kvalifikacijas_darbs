<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DanceGroupMemberResource;
use App\Models\DanceGroupMember;
use App\Http\Requests\DanceGroupMemberRequest;
use App\Models\DanceGroup;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DancerRequest;

class DanceGroupMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = DanceGroupMember::all();
        $members->load('ageGroup', 'appUser', 'danceGroup');

        return DanceGroupMemberResource::collection($members);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DanceGroupMemberRequest $request)
    {
        $validated = $request->validated();

        $member = DanceGroupMember::create($validated);

        return (new DanceGroupMemberResource($member))->response()->setStatusCode(201);
    }


    public function leaderStore(DanceGroupMemberRequest $request){
         $validated = $request->validated();

         $member = DanceGroupMember::create([
            'dance_group_id' => $validated['dance_group_id'],
            'user_id' => Auth::id(),
            'role' => 'leader',       
            'status' => 'waiting'    
        ]);

        return (new DanceGroupMemberResource($member))
            ->response()
            ->setStatusCode(201);
    }


    /**
     * Display the specified resource.
     */
    public function show(DanceGroupMember $member)
    {
        $member->load('ageGroups', 'appUsers', 'danceGroups');
        return new DanceGroupMemberResource($member);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DanceGroupMember $member)
    {
        $member->delete();
        return new DanceGroupMemberResource($member);
    }
    public function showDancers(DanceGroup $group)
    {
        $dancers = DanceGroupMember::where('status', 'waiting')
            ->where('role', 'dancer')
            ->where('dance_group_id', $group->id)
            ->get();

        return DanceGroupMemberResource::collection($dancers);
    }

public function approveMember(DancerRequest $request)
{
    $memberId = $request->id;
    $role = $request->role;

    if (!in_array($role, ['dancer', 'leader'])) {
        return response()->json([
            'message' => 'Invalid role'
        ], 422);
    }

    DanceGroupMember::where('id', $memberId)
        ->where('role', $role)
        ->update([
            'status' => 'approved'
        ]);

    return response()->json([
        'message' => 'ok'
    ]);
}

public function declineMember(DancerRequest $request)
{
    $memberId = $request->id;
    $role = $request->role;

    if (!in_array($role, ['dancer', 'leader'])) {
        return response()->json([
            'message' => 'Invalid role'
        ], 422);
    }

    DanceGroupMember::where('id', $memberId)
        ->where('role', $role)
        ->update([
            'status' => 'declined'
        ]);

    return response()->json([
        'message' => 'ok'
    ]);
}


    public function showLeaders(DanceGroup $group)
    {
        $dancers = DanceGroupMember::where('status', 'waiting')
            ->where('role', 'leader')
            ->where('dance_group_id', $group->id)
            ->get();

        return DanceGroupMemberResource::collection($dancers);
    }
}
