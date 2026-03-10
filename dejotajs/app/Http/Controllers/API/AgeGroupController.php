<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AgeGroup;
use App\Http\Resources\AgeGroupResource;
use App\Http\Requests\AgeGroupRequest;
use App\Models\DanceGroupMember;
use Illuminate\Support\Facades\Auth;

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
        // Validēt pārējos laukus (name, age_group)
        $validated = $request->validated();

        $ageGroup = AgeGroup::create($validated);

        return (new AgeGroupResource($ageGroup))
            ->response()
            ->setStatusCode(201);
    }

    public function getAdmissonAgeGroups()
    {
        $ageGroups = AgeGroup::with(['admissions' => function($query) {
                $query->whereDate('end_date', '>=', now());
            }])
            ->where('status_admission', true)
            ->get();

        foreach ($ageGroups as $ageGroup) {

            if ($ageGroup->admissions->isEmpty()) {
                $ageGroup->status_admission = false;
                $ageGroup->save();
            }

        }

        return AgeGroupResource::collection($ageGroups);
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
