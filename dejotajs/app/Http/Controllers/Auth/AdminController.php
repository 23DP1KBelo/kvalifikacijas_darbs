<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DanceGroup;
use App\Http\Resources\DanceGroupResource;
use App\Models\DanceGroupMember;
use App\Http\Resources\AdminDanceGroupResource;
use App\models\AppUser;
use App\http\Resources\AppUserResource;
use App\Models\Post;
use App\Models\Event;
use App\Http\Resources\PostResource;
use App\Http\Resources\EventResource;


class AdminController extends Controller
{
    public function showDanceGroups()
    {
        $groups = DanceGroup::where('status', 'waiting')->get();

        return DanceGroupResource::collection($groups);
    }

    public function approveDanceGroup(Request $request){
        $groupId = $request->id;

        DanceGroup::where('id', $groupId)->update([
            'status' => 'approved'
        ]);

        DanceGroupMember::where('dance_group_id', $groupId)
        ->update([
            'status' => 'approved'
        ]);

        return response()->json([
            'message' => 'Veiksmīgi apstiprināts deju kolektīvs'
        ]);
    }

        public function declineDanceGroup(Request $request){
        $groupId = $request->id;

        DanceGroup::where('id', $groupId)->update([
            'status' => 'declined'
        ]);

        DanceGroupMember::where('dance_group_id', $groupId)
        ->update([
            'status' => 'declined'
        ]);

        return response()->json([
            'message' => 'Veiksmīgi neapstriprināts deju kolektīvs'
        ]);
    }

    public function DanceGroupShow(DanceGroup $group)
    {
        $group->load([
            'members' => function ($query) {
                $query->with('appUser');
            },
        ]);

        return new AdminDanceGroupResource($group);
    }

    public function UserSearch(Request $request)
    {
        $search = trim($request->query('q', ''));

        $users = AppUser::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('surname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate(10);

        return AppUserResource::collection($users);
    }

    public function DanceGroupSearch(Request $request)
    {
        $queryText = trim($request->query('q', ''));

        $groups = DanceGroup::where('status', 'approved')
            ->when($queryText !== '', function ($query) use ($queryText) {
                $query->where(function ($q) use ($queryText) {
                    $q->where('name', 'like', '%' . $queryText . '%')
                        ->orWhereHas('members', function ($memberQuery) use ($queryText) {
                            $memberQuery->where('role', 'leader')
                                ->where('status', 'approved')
                                ->whereHas('appUser', function ($userQuery) use ($queryText) {
                                    $userQuery->where('name', 'like', '%' . $queryText . '%')
                                        ->orWhere('surname', 'like', '%' . $queryText . '%');
                                });
                        });
                });
            })
            ->with([
                'ageGroups',
                'members' => fn($q) => $q
                    ->where('status', 'approved')
                    ->where('role', 'leader')
                    ->with('appUser'),
            ])
            ->paginate((int) $request->input('per_page', 10));

        return DanceGroupResource::collection($groups);
    }


    public function PostSearch(Request $request)
    {
        $search = trim($request->query('q', ''));

        $posts = Post::query()
            ->with([
                'danceGroupMember.appUser',
                'danceGroupMember.danceGroup',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhereHas('danceGroupMember.danceGroup', function ($groupQuery) use ($search) {
                            $groupQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate((int) $request->query('per_page', 10));

        return PostResource::collection($posts);
    }


    public function EventSearch(Request $request)
    {
        $search = trim($request->query('q', ''));

        $events = Event::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return EventResource::collection($events);
    }
}
