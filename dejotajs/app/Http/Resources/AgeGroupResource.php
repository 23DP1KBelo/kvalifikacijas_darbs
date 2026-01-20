<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgeGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'age_group' => $this->age_group,
            'status_admission' => $this->status_admission,
            'dance_group_id' => $this->dance_group_id,
            'created_at' => $this->created_at,
        ];
    }
}
