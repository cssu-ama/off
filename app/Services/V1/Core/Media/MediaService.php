<?php

namespace App\Services\V1\Core\Media;

use App\Http\Resources\V1\Core\File\MediaCollection;
use App\Http\Resources\V1\Core\File\MediaListCollection;
use App\Jobs\V1\Core\Media\UploadFileToS3Job;
use App\Models\V1\Core\Media\Media;
use App\Services\V1\Core\Media\Interfaces\MediaServiceInterface;
use App\Traits\V1\Core\Cache\ClearsRedisCache;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Plank\Mediable\Facades\MediaUploader;
use Plank\Mediable\Media as MediaAlias;

/**
 * Class FileService.
 */
class MediaService implements MediaServiceInterface
{
    use ClearsRedisCache;

    public function index(bool $hasPaginate): array
    {
        $cacheKey = $this->getMediasCacheKey('admin');

        if ($cached = $this->getMediasFromCache($cacheKey)) {
            return $cached;
        }

        $medias = $this->getMedias($hasPaginate);

        $this->cacheMedias($cacheKey, $medias);

        return $medias;
    }

    public function store(array $inputs): Media|Model
    {
        $fileName = now()->timestamp . '_' . uniqid();

        $uploader = MediaUploader::fromSource($inputs['file'])
            ->useFilename($fileName)
            ->toDestination('public', $inputs['subfolder']);

        if (filled($inputs['alt'] ?? null)) {
            $uploader->withAltAttribute($inputs['alt']);
        }

        $media = $uploader->upload();

        UploadFileToS3Job::dispatch($media, 'medias:*');

        $this->clearMediasCache();

        return $media;
    }

    public function destroy(Media $media): Media|Model
    {
        if (!$media->delete()) {
            throw new Exception('مشکل در حذف فایل');
        }

        $this->clearMediasCache();

        return $media;
    }

    public function indexTypes(): array
    {
        return [
            MediaAlias::TYPE_IMAGE, // Because the TYPE_IMAGE_VECTOR & TYPE_IMAGE are in the same group it is not necessary to add both in list
            MediaAlias::TYPE_PDF,
            MediaAlias::TYPE_AUDIO,
            MediaAlias::TYPE_VIDEO,
            MediaAlias::TYPE_ARCHIVE,
            MediaAlias::TYPE_DOCUMENT,
            MediaAlias::TYPE_SPREADSHEET,
            MediaAlias::TYPE_PRESENTATION,
        ];
    }

    public function attachFile(array $options): void
    {
        $file = $options['file'];
        $model = $options['model'];
        $modelName = $options['modelName'];
        $subfolder = $options['subfolder'];
        $mediaTagName = $options['mediaTagName'];

        $cacheKeyToInvalidate = $options['cacheKey'];

        $fileName =
            now()->timestamp .
            '_' .
            uniqid();

        $media = MediaUploader::fromSource($file)
            ->useFilename($fileName)
            ->toDestination('public', $modelName . '/' . $subfolder)
            ->upload();

        $model->syncMedia($media, $mediaTagName);

        UploadFileToS3Job::dispatch($media, $cacheKeyToInvalidate ?? null);

        $this->clearMediasCache();
    }

    public function deleteAttachedFile(array $options): void
    {
        $model = $options['model'];
        $mediaTagName = $options['mediaTagName'];

        $media = $model
            ->getMedia($mediaTagName)
            ->first();

        if ($media) {
            $media->delete();
        }

        $this->clearMediasCache();
    }

    private function getMediasCacheKey(string $cacheContext): string
    {
        $queryParams = request()->query();
        ksort($queryParams);

        return "medias:{$cacheContext}:" . md5(
                request()->path() .
                serialize($queryParams)
            );
    }

    private function getMediasFromCache(string $cacheKey): ?array
    {
        $data = Redis::get($cacheKey);

        return $data
            ? json_decode($data, true)
            : null;
    }

    private function getMedias(bool $hasPaginate): array
    {
        $query = Media::query()
            ->ignoreRequest(['per_page'])
            ->filter();

        if ($hasPaginate) {
            $perPage = request()->query(
                'per_page',
                config('eloquentFilter.max_limit', 20)
            );

            return MediaCollection::make(
                $query->paginate($perPage, ['*'], 'page')
            )->resolve();
        }

        return MediaListCollection::make(
            $query->get()
        )->resolve();
    }

    private function cacheMedias(string $cacheKey, array $data): void
    {
        Redis::setex(
            $cacheKey,
            10 * 60,
            json_encode($data)
        );
    }

    private function clearMediasCache(): void
    {
        $this->clearCache('users:*');
        $this->clearCache('medias:*');
        $this->clearCache('comments:*');
        $this->clearCache('posts:*');
        $this->clearCache('categories:*');
        $this->clearCache('tags:*');
    }
}
