<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdmissionRequest;
use App\Http\Resources\AdmissionResource;
use App\Models\Admission;
use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admissions = Admission::all();

        $admissions->load('ageGroup');
        return AdmissionResource::collection($admissions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdmissionRequest $request)
    {
        $validated = $request->validated();
        $admission = Admission::create($validated);

        return (new AdmissionResource($admission))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Admission $admission)
    {
        $admission->load('ageGroup');
        return new AdmissionResource($admission);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admission $admission)
    {
        $admission->delete();
        return new AdmissionResource($admission);
    }
}
