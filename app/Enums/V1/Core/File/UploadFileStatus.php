<?php

namespace App\Enums\V1\Core\File;

enum UploadFileStatus: string
{
    case PENDING = 'pending';
    case UPLOADED = 'uploaded';
}
