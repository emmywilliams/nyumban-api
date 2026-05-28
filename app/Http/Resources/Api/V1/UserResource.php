<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'role' => optional($this->role)->name,
            'avatar_url' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'status' => $this->status,
            'joined_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
