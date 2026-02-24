<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DanceGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $members = $this->whenLoaded('members', function () {
            return $this->members; 
        });
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'city' => $this->city,
            'address' => $this->address,
            'picture_url' => $this->picture ? asset('storage/' . $this->picture) : null,
            'approval_url' => $this->approval ? asset('storage/' . $this->approval) : null,
            'status' => $this->status,
            'gener' => $this->gener,
            'created_at' => $this->created_at,

            'age_groups' => AgeGroupResource::collection(
                $this->whenLoaded('ageGroups')
            ),
            'members' => DanceGroupMemberResource::collection($members),
            'leaders' => DanceGroupMemberResource::collection(
            $members instanceof \Illuminate\Support\Collection 
                ? $members->filter(fn($member) => $member->role === 'leader') 
                : collect()
            ),
        ];
    }
}
