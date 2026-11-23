<?php

namespace App\Jobs\V1\Core\Media;

use App\Traits\V1\Core\Cache\ClearsRedisCache;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Plank\Mediable\Exceptions\MediaMoveException;
use Plank\Mediable\Media;

class UploadFileToS3Job implements ShouldQueue
{
    use Queueable;

    use ClearsRedisCache;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Media $media, protected ?string $cacheKeyToInvalidate = null)
    {
        $this->queue = 'upload';
    }

    /**
     * Execute the job.
     * @throws MediaMoveException
     */
    public function handle(): void
    {
        try {
            $fullPath = storage_path('app/public/' . $this->media->getDiskPath());

            if (!file_exists($fullPath)) {
                throw new Exception('File not found: ' . $fullPath);
            }

            $environment = config('app.env');

            $this->media
                ->moveToDisk(
                    's3',
                    "files/{$environment}"
                );

            if ($this->cacheKeyToInvalidate) {
                $this->clearCache($this->cacheKeyToInvalidate . ':*');
            }

        } catch (Exception $exception) {
            Log::error('S3 file upload failed', [
                'media_id' => $this->media->getKey(),
                'exception' => $exception,
            ]);

            throw $exception;
        }
    }
}
