<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DanceGroupResource;
use App\Http\Requests\DanceGroupRequest;
use Illuminate\Http\Request;
use App\Models\DanceGroup;

class DanceGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = DanceGroup::all();
        return DanceGroupResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DanceGroupRequest $request)
    {
        $validated = $request->validated();
        $danceGroup = DanceGroup::create($validated);

        return (new DanceGroupResource($danceGroup))->response()->setStatusCode(201);

    }

    /**
     * Display the specified resource.
     */
    public function show(DanceGroup $danceGroup)
    {
        return new DanceGroupResource($danceGroup);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DanceGroupRequest $request, DanceGroup $danceGroup)
    {
        $validated = $request->validated();

        $danceGroup->update($validated);

        return new DanceGroupResource($danceGroup);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DanceGroup $danceGroup)
    {
        $danceGroup->delete();

        return new DanceGroupResource($danceGroup);
    }
}
