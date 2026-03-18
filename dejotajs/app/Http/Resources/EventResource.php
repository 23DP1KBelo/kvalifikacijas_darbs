<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "location" => $this->location,
            "date_start" => $this->date_start,
            "date_end" => $this->date_end,
            "created_at" => $this->created_at,
            "dance_group_member" => [
                "id" => $this->member?->id,
                "name" => $this->member?->danceGroup?->name
            ],
            "dance_groups" => AgeGroupResource::collection($this->whenLoaded('ageGroups')),
        ];
    }
}
