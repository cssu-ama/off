<?php

namespace App\Http\Resources\V1\Core\File;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Plank\Mediable\Media as MediaAlias;

class MediaTypesResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $labels = [
            MediaAlias::TYPE_IMAGE => 'تصویر',
            MediaAlias::TYPE_PDF => 'فایل PDF',
            MediaAlias::TYPE_AUDIO => 'فایل صوتی',
            MediaAlias::TYPE_VIDEO => 'ویدئو',
            MediaAlias::TYPE_ARCHIVE => 'فایل فشرده',
            MediaAlias::TYPE_DOCUMENT => 'سند',
            MediaAlias::TYPE_SPREADSHEET => 'صفحه گسترده',
            MediaAlias::TYPE_PRESENTATION => 'ارائه',
        ];

        return [
            'value' => $this->resource,
            'label' => $labels[$this->resource] ?? 'سایر'
        ];
    }
}
