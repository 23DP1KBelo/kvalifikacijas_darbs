<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'private' => $this->private === 1 ? 'Privāts' : 'publisks',
            'picture' => $this->picture ? asset('storage/' . $this->picture) : null,
            'created_at' => $this->created_at,
            'dance_group_member' => [
                'dance_group_member_id' => $this->dance_group_member_id,
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
