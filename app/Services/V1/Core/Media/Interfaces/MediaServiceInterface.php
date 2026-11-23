<?php

namespace App\Services\V1\Core\Media\Interfaces;

use App\Models\V1\Core\Media\Media;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Exceptions\MediaUpload\ConfigurationException;
use Plank\Mediable\Exceptions\MediaUpload\FileExistsException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotFoundException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotSupportedException;
use Plank\Mediable\Exceptions\MediaUpload\FileSizeException;
use Plank\Mediable\Exceptions\MediaUpload\ForbiddenException;
use Plank\Mediable\Exceptions\MediaUpload\InvalidHashException;

interface MediaServiceInterface
{
    /**
     * @param bool $hasPaginate
     * @return array
     * @throws Exception
     */
    public function index(bool $hasPaginate): array;

    /**
     * @param array $inputs {
     *     file: UploadedFile,
     *     subfolder: string
     * }
     * @return Media|Model
     * @throws FileNotSupportedException
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws ForbiddenException
     * @throws FileSizeException
     * @throws InvalidHashException
     * @throws ConfigurationException
     */
    public function store(array $inputs): Media|Model;

    /**
     * @param Media $media
     * @return Media|Model
     * @throws Exception
     */
    public function destroy(Media $media): Media|Model;

    /**
     * @return array
     */
    public function indexTypes(): array;

    /**
     * @param array $options {
     *     file: UploadedFile,
     *     model: Model,
     *     modelName: string,
     *     subfolder: string,
     *     mediaTagName: string
     * }
     * @throws FileNotSupportedException
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @throws ForbiddenException
     * @throws FileSizeException
     * @throws InvalidHashException
     * @throws ConfigurationException
     */
    public function attachFile(array $options): void;

    /**
     * @param array $options {
     *     model: Model,
     *     mediaTagName: string
     * }
     */
    public function deleteAttachedFile(array $options): void;
}
