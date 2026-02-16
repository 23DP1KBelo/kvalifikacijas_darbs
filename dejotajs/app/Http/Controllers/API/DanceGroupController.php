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
        $danceGroups = DanceGroup::all();
        $danceGroups->load('ageGroups', 'members');
        return DanceGroupResource::collection($danceGroups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DanceGroupRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('picture')) {
            $validated['picture'] = $request->file('picture')->store('dance_images', 'public');
        }

        if ($request->hasFile('approval')) {
            $validated['approval'] = $request->file('approval')->store('dance_documents', 'public');
        }

        $danceGroup = DanceGroup::create($validated);

        return (new DanceGroupResource($danceGroup))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DanceGroup $danceGroup)
    {
        $danceGroup->load('ageGroups', 'members');

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
