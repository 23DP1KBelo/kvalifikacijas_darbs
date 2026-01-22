<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'private' => $this->private === 1 ? 'Privāts' : 'publisks',
            'created_at' => $this->created_at,
            
            'dance_group_member' => [
                'user' => [
                    'name' => $this->danceGroupMember->appUser?->name,
                ],
                'dance_group' => [
                    'name' => $this->danceGroupMember->danceGroup?->name,
                ],
            ]
        ];
    }
}
