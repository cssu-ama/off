<?php

namespace App\Services\V1\Banner\Interfaces;

use App\Models\V1\Banner\Banner;
use Exception;
use Illuminate\Database\Eloquent\Model;

interface BannerServiceInterface
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
     * @return Banner|Model
     * @throws Exception
     */
    public function store(array $inputs): Banner|Model;

    /**
     * @param array $inputs
     * @param Banner $banner
     * @return Banner|Model
     * @throws Exception
     */
    public function update(array $inputs, Banner $banner): Banner|Model;

    /**
     * @return array
     * @throws Exception
     */
    public function indexStatus(): array;

    /**
     * @param array $inputs
     * @param Banner $banner
     * @return Banner|Model
     */
    public function changeStatus(array $inputs, Banner $banner): Banner|Model;

    /**
     * @return array
     * @throws Exception
     */
    public function indexPlacement(): array;

    /**
     * @return array
     * @throws Exception
     */
    public function indexLinkRelation(): array;

    /**
     * @param Banner $banner
     * @return Banner|Model
     * @throws Exception
     */
    public function destroy(Banner $banner): Banner|Model;
}
