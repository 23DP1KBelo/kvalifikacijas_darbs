<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Http\Resources\EventResource;
use App\Http\Requests\EventRequest;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::with([
            'member.danceGroup',
            'ageGroups.danceGroup', 
            'ageGroups.admissions'
        ])->get();

        return EventResource::collection($events);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request)
    {
        $validated = $request->validated();
        $event = Event::create($validated);

        return (new EventResource($event->load('member.danceGroup')))->response()->setStatusCode(201);
    }

    public function attachAgeGroups(Request $request, Event $event)
    {
        $validated = $request->validate([
            'age_group_ids' => 'required|array',
            'age_group_ids.*' => 'exists:age_groups,id',
        ]);
        $event->ageGroups()->attach($validated['age_group_ids']);
        $eventId = $event->id;

        return response()->json([
            'message' => 'Age groups attached successfully',
            'event' => $event->load('ageGroups'),
            'event_id' => $eventId
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load('member.danceGroup');
        return new EventResource($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $event->update([
            'name' => $request->name,
            'location' => $request->location,
            'description' => $request->description,
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
        ]);

        return new EventResource($event);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->ageGroups()->detach();
        $event->delete();

        return new EventResource($event);
    }
}
