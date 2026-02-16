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
            'members' => DanceGroupMemberResource::collection(
                $this->whenLoaded('members')
            ),
        ];
    }
}
