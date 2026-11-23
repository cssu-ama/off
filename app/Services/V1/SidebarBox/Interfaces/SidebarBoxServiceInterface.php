<?php

namespace App\Services\V1\SidebarBox\Interfaces;

use App\Models\V1\SidebarBox\SidebarBox;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Throwable;

interface SidebarBoxServiceInterface
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
     * @return array
     * @throws Exception
     */
    public function indexPlacement(): array;

    /**
     * @return array
     * @throws Exception
     */
    public function indexPostSelectionType(): array;

    /**
     * @param array $inputs
     * @return SidebarBox|Model
     * @throws Exception
     * @throws Throwable
     */
    public function store(array $inputs): SidebarBox|Model;

    /**
     * @param array $inputs
     * @param SidebarBox $sidebarBox
     * @return SidebarBox|Model
     * @throws Exception
     * @throws Throwable
     */
    public function update(array $inputs, SidebarBox $sidebarBox): SidebarBox|Model;

    /**
     * @param array $inputs
     * @return void
     * @throws Throwable
     */
    public function changeOrder(array $inputs): void;

    /**
     * @param array $inputs
     * @param SidebarBox $sidebarBox
     * @return void
     * @throws Throwable
     */
    public function changePostOrder(array $inputs, SidebarBox $sidebarBox): void;

    /**
     * @param SidebarBox $sidebarBox
     * @return SidebarBox|Model
     * @throws Exception
     */
    public function destroy(SidebarBox $sidebarBox): SidebarBox|Model;
}
