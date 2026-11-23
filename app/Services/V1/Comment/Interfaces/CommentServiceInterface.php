<?php

namespace App\Services\V1\Comment\Interfaces;

use App\Models\V1\Comment\Comment;
use App\Models\V1\Post\Post;
use Exception;
use Illuminate\Database\Eloquent\Model;

interface CommentServiceInterface
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
     * @return Comment|Model
     * @throws Exception
     */
    public function store(array $inputs): Comment|Model;

    /**
     * @param Comment $comment
     * @return Comment|Model
     * @throws Exception
     */
    public function destroy(Comment $comment): Comment|Model;

    /**
     * @return array
     * @throws Exception
     */
    public function indexStatus(): array;

    /**
     * @param array $inputs
     * @param Comment $comment
     * @return Comment|Model
     */
    public function changeStatus(array $inputs, Comment $comment): Comment|Model;
}
