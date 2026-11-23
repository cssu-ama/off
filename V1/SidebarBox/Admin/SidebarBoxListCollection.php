<?php

namespace App\Http\Resources\V1\SidebarBox\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SidebarBoxListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => SidebarBoxResource::collection($this->collection)
        ];
    }
}
