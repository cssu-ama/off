<?php

namespace App\Http\Resources\V1\Core\File;

use App\Enums\V1\Core\File\UploadFileStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
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
            'filename' => $this->filename,
            'alt' => $this->alt,
            'extension' => $this->extension,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'variant_name' => $this->variant_name,
            'url' =>
                $this->disk !== 's3' ?
                    null :
                    $this->getUrl(),
            'status' =>
                $this->disk !== 's3' ?
                    UploadFileStatus::PENDING :
                    UploadFileStatus::UPLOADED
        ];
    }
}
