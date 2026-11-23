<?php

namespace App\Http\Resources\V1\User\Public;

use App\Http\Resources\V1\Core\File\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicUserResource extends JsonResource
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
            'phone' => $this->phone,
            'user_name' => $this->user_name,

            'avatar' => MediaResource::make($this->getMedia('avatar')->first()),
        ];
    }
}
