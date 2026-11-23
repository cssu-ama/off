<?php

namespace App\Http\Resources\V1\User\Admin;

use App\Http\Resources\V1\Core\File\MediaResource;
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
            'id' => $this->id,
            'name' => $this->name ?? "",
            'phone' => $this->phone,
            'user_name' => $this->user_name,

            'roles' => $this->roles->pluck('name')->map(fn ($role) => strtoupper($role)),

            'avatar' => MediaResource::make($this->getMedia('avatar')->first()),
        ];
    }
}
