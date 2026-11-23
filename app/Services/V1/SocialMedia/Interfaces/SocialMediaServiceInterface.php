<?php

namespace App\Services\V1\SocialMedia\Interfaces;

use App\Models\V1\SocialMedia\SocialMedia;
use Exception;
use Illuminate\Database\Eloquent\Model;

interface SocialMediaServiceInterface
{
    /**
     * @param bool $hasPaginate
     * @return array
     * @throws Exception
     */
    public function index(bool $hasPaginate): array;

    /**
     * @param bool $hasPaginate
     * @return array
     * @throws Exception
     */
    public function publicIndex(bool $hasPaginate): array;

    /**
     * @param array $inputs
     * @return SocialMedia|Model
     * @throws Exception
     */
    public function store(array $inputs): SocialMedia|Model;

    /**
     * @param array $inputs
     * @param SocialMedia $socialMedia
     * @return SocialMedia|Model
     * @throws Exception
     */
    public function update(array $inputs, SocialMedia $socialMedia): SocialMedia|Model;

    /**
     * @param SocialMedia $socialMedia
     * @return SocialMedia|Model
     * @throws Exception
     */
    public function destroy(SocialMedia $socialMedia): SocialMedia|Model;
}
