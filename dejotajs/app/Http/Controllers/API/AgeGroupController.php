<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AgeGroup;
use Illuminate\Http\Request;
use App\Http\Resources\AgeGroupResource;
use App\Http\Requests\AgeGroupRequest;


class AgeGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ageGroups = AgeGroup::all();

        return AgeGroupResource::collection($ageGroups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AgeGroupRequest $request)
    {
        $validated = $request->validated();

        $ageGroup = AgeGroup::create($validated);

        return (new AgeGroupResource($ageGroup))->response()->setStatusCode(201);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(AgeGroup $ageGroup)
    {
        return new AgeGroupResource($ageGroup);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AgeGroupRequest $request, AgeGroup $ageGroup)
    {
        $validated = $request->validated();

        $ageGroup->update($validated);

        return new AgeGroupResource($ageGroup);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AgeGroup $ageGroup)
    {
        $ageGroup->delete();

        return 'deleted';
    }
}
