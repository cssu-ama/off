<?php

namespace App\Services\V1\Category\Interfaces;

use App\Models\V1\Category\Category;
use Exception;
use Illuminate\Database\Eloquent\Model;

interface CategoryServiceInterface
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
     * @param int $id
     * @return Category|Model
     */
    public function find(int $id): Category|Model;

    /**
     * @param array $inputs
     * @return Category|Model
     * @throws Exception
     */
    public function store(array $inputs): Category|Model;

    /**
     * @param array $inputs
     * @param Category $category
     * @return Category|Model
     * @throws Exception
     */
    public function update(array $inputs, Category $category): Category|Model;

    /**
     * @param Category $category
     * @return Category|Model
     * @throws Exception
     */
    public function destroy(Category $category): Category|Model;
}
