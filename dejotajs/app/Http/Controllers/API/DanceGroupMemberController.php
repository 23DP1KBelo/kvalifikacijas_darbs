<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DanceGroupMemberResource;
use App\Models\DanceGroupMember;
use Illuminate\Http\Request;
use App\Http\Requests\DanceGroupMemberRequest;

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
}
