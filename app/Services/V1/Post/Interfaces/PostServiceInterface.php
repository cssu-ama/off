<?php

namespace App\Services\V1\Post\Interfaces;

use App\Models\V1\Post\Post;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Throwable;

interface PostServiceInterface
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
     * @param Post $post
     * @return Post|Model
     */
    public function show(Post $post): Post|Model;

    /**
     * @param int $id
     * @return Post|Model
     */
    public function find(int $id): Post|Model;

    /**
     * @param array $inputs
     * @return Post|Model
     * @throws Exception
     * @throws Throwable
     */
    public function store(array $inputs): Post|Model;

    /**
     * @param array $inputs
     * @param Post $post
     * @return Post|Model
     * @throws Exception
     * @throws Throwable
     */
    public function update(array $inputs, Post $post): Post|Model;

    /**
     * @return array
     * @throws Exception
     */
    public function indexStatus(): array;

    /**
     * @param array $inputs
     * @param Post $post
     * @return Post|Model
     */
    public function changeStatus(array $inputs, Post $post): Post|Model;

    /**
     * @param array $inputs
     * @return void
     * @throws Throwable
     */
    public function changeOrder(array $inputs): void;

    /**
     * @param Post $post
     * @return Post|Model
     * @throws Exception
     */
    public function destroy(Post $post): Post|Model;
}
