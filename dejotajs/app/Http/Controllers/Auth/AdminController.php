<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DanceGroup;
use App\Http\Resources\DanceGroupResource;
use App\Models\DanceGroupMember;

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
}
