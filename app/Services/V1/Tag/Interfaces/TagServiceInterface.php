<?php

namespace App\Services\V1\Tag\Interfaces;

use App\Models\V1\Tag\Tag;
use Exception;
use Illuminate\Database\Eloquent\Model;

interface TagServiceInterface
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
     * @throws Exception
     * @param array $inputs
     * @return Tag|Model
     */
    public function store(array $inputs): Tag|Model;

    /**
     * @param array $inputs
     * @param Tag $tag
     * @return Tag|Model
     * @throws Exception
     */
    public function update(array $inputs, Tag $tag): Tag|Model;

    /**
     * @param Tag $tag
     * @return Tag|Model
     * @throws Exception
     */
    public function destroy(Tag $tag): Tag|Model;
}
