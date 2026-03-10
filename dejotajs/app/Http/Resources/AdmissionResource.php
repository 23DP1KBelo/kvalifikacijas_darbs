<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdmissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "name" => $this->name,
            "start_date" => $this->start_date,
            "end_date" => $this->end_date,
            
            "age_group" => [
                "id" => $this->ageGroup->id,
                "name" => $this->ageGroup->name,
                "age_group" => $this->ageGroup->age_group
            ]
        ];
    }
}
