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
            'picture'=> $this->picture,
            'approval'=> $this->approval,
            'status' => $this->status,
            'gener' => $this->gener,
            'created_at' => $this->created_at,
        ];
    }
}
