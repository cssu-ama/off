<?php

namespace App\Models\V1\SidebarBox;

use App\Models\V1\Post\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SidebarBoxPost extends Pivot
{
    protected $table = 'sidebar_box_post';

    protected $fillable = [
        'post_id',
        'sidebar_box_id',
        'post_order'
    ];

    public $incrementing = true;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'post_order' => 'integer',
        ];
    }

    public function sidebarBox(): BelongsTo
    {
        return $this->belongsTo(SidebarBox::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
