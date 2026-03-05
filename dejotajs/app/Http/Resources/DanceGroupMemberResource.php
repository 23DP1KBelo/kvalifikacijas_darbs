<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DanceGroupMemberResource extends JsonResource
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
            'status' => $this->status,
            'role' => $this->role,

            'user' => [
                'id' => $this->appUser->id,
                'name' => $this->appUser->name,
                'surname' => $this->appUser->surname,
            ],

            'dance_group' => [
                'id' => $this->danceGroup->id,
                'name' => $this->danceGroup->name,
                'status' => $this->danceGroup->status,
            ],

            'age_group' => [
                'id' => $this->ageGroup?->id,
                'name' => $this->ageGroup?->name,
                'age_group' => $this->ageGroup?->age_group
            ]
        ];
    }
}
