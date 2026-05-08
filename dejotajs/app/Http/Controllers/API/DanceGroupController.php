<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DanceGroupResource;
use App\Http\Requests\DanceGroupRequest;
use Illuminate\Http\Request;
use App\Models\DanceGroup;
use Illuminate\Support\Facades\Auth;
use App\Models\DanceGroupMember;
use App\Http\Requests\DanceGroupUpdateRequest;

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

    public function getAllGroups(){
        $danceGroups = DanceGroup::where('status', 'approved')->get();
        $danceGroups->load('ageGroups', 'members');
        return DanceGroupResource::collection($danceGroups);
    }

    public function groupList()
    {
        $userId = Auth::id();

        $danceGroups = DanceGroup::leftJoin('dance_group_members', function ($join) use ($userId) {
                $join->on('dance_groups.id', '=', 'dance_group_members.dance_group_id')
                    ->where('dance_group_members.user_id', '=', $userId);
            })
            ->where('dance_groups.status', 'approved') 
            ->whereNull('dance_group_members.user_id')
            ->select('dance_groups.*')
            ->get();

        return DanceGroupResource::collection($danceGroups);
    }

    public function groupSorting($sort){
        $danceGroups = DanceGroup::where('status', 'approved')
            ->orderBy('name', $sort)
            ->get();

        $danceGroups->load('ageGroups', 'members');
        return DanceGroupResource::collection($danceGroups);
    }

    public function filterOptions() {
        $cities = DanceGroup::where('status', 'approved')
            ->select('city')
            ->distinct()
            ->pluck('city');

        $genres = DanceGroup::where('status', 'approved')
            ->select('genre')
            ->distinct()
            ->pluck('genre');

        $ageGroups = DanceGroup::where('status', 'approved')
            ->join('age_groups', 'dance_groups.id', '=', 'age_groups.dance_group_id')
            ->whereNotNull('age_groups.age_group')
            ->select('age_groups.age_group')
            ->distinct()
            ->pluck('age_group');

        return response()->json([
            'cities' => $cities,
            'genres' => $genres,
            'age_groups' => $ageGroups,
        ]);
    }

    public function filterGroups () {
        $query = DanceGroup::query();

        If(request()->filled('city')) {
            $query->where('city', request('city'));
        }

        If(request()->filled('genre')) {
            $query->where('genre', request('genre'));
        }

        $danceGroups = $query->where('status', 'approved')->get();
        $danceGroups->load('ageGroups', 'members');
        return DanceGroupResource::collection($danceGroups);
    }


    public function groupListApprovedMember()
    {
        $userId = Auth::id();

        $danceGroups = DanceGroup::with(['members' => function($query) {
                $query->where('status', 'approved'); // tikai apstiprinātie dalībnieki
            }])
            ->join('dance_group_members', function ($join) use ($userId) {
                $join->on('dance_groups.id', '=', 'dance_group_members.dance_group_id')
                    ->where('dance_group_members.user_id', '=', $userId);
            })
            ->where('dance_groups.status', 'approved') 
            ->where('dance_group_members.status', 'approved')
            ->select('dance_groups.*')
            ->get();

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

        DanceGroupMember::create([
            'status' => 'waiting',
            'role' => 'leader',
            'user_id' => Auth::id(),
            'dance_group_id' => $danceGroup->id
        ]);

        return (new DanceGroupResource($danceGroup))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DanceGroup $danceGroup)
    {
        $danceGroup->load([
            'members' => function ($query) {
                $query->where('status', 'approved')
                    ->with('appUser');
            },
            'ageGroups' => function ($query) {
            $query->with(['admissions' => function ($query) {
                $query->whereDate('start_date', '<=', now())
                      ->whereDate('end_date', '>=', now());
            }]);
        }
        ]);

        return new DanceGroupResource($danceGroup);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(DanceGroupUpdateRequest $request, DanceGroup $danceGroup)
    {
        $validated = $request->validated();

        $danceGroup->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'city' => $validated['city'],
            'address' => $validated['address'],
        ]);

        return new DanceGroupResource($danceGroup);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $danceGroup = DanceGroup::findOrFail($id);
        $members = $danceGroup->members()->with('posts')->get();

        foreach ($members as $member) {
        // Dzēšam visus postus, kas pieder šim dalībniekam
        $member->posts()->delete();
        }

        $danceGroup->members()->delete();
        $danceGroup->delete();

        return response()->json(['message' => 'Kolektīvs veiksmīgi dzēsts'], 200);
    }


    public function search(Request $request)
    {
        $queryText = $request->input('q');

        $danceGroups = DanceGroup::where('status', 'approved')
            ->when($queryText, function ($query) use ($queryText) {
                $query->where(function ($q) use ($queryText) {
                    $q->where('name', 'like', $queryText . '%')
                        ->orWhereHas('members', function ($memberQuery) use ($queryText) {
                            $memberQuery->where('role', 'leader')
                                ->whereHas('appUser', function ($userQuery) use ($queryText) {
                                    $userQuery->where('name', 'like', $queryText . '%')
                                        ->orWhere('surname', 'like', $queryText . '%');
                                });
                        });
                });
            })
            ->with([
                'ageGroups',
                'members' => fn($q) => $q
                    ->where('status', 'approved')
                    ->where('role', 'leader')
                    ->with('appUser')
            ])
            ->get();

        return DanceGroupResource::collection($danceGroups);
    }

    public function leaderGroups(){

        /** @var \App\Models\AppUser $user */
        $user = Auth::user();

        $groups = $user->danceGroupMembers()
            ->where('role', 'leader')
            ->whereHas('danceGroup', function ($q) {$q->where('status', 'approved');})
            ->with('danceGroup')
            ->get()
            ->map(function ($member) {
                return [
                    'member_id' => $member->id,
                    'group_id' => $member->danceGroup->id,
                    'name' => $member->danceGroup->name,
                ];
            });

        return response()->json($groups);
    }
}
