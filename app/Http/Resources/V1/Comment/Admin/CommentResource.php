<?php

namespace App\Http\Resources\V1\Comment\Admin;

use App\Http\Resources\V1\User\Admin\AuthorResource;
use App\Models\V1\Post\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $commentable = $this->commentable;

        return [
            'id' => $this->id,
            'comment' => $this->comment,

            'status' => $this->status,

            'reply_to' => CommentResource::make($this->publishedParent),

            'title' => $this->title ?? '',
            'author_name' => $this->author_name ?? '',
            'phone' => $this->phone ?? '',
            'email' => $this->email ?? '',

            'post' => $commentable instanceof Post
                ? [
                    'id' => $commentable->id,
                    'title' => $commentable->title,
                    'slug' => $commentable->slug,
                ]
                : null,
        ];
    }
}
