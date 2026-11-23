<?php

namespace App\ModelFilters\V1\Core\Media;

use App\Traits\V1\Core\Sanitization\NormalizesSearchContent;
use Illuminate\Database\Eloquent\Builder;
use Plank\Mediable\Media as MediaAlias;

trait MediasFilter
{
    use NormalizesSearchContent;

    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function filterCustomsearch(Builder $builder, $value): Builder
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value));

        return $builder->where(function ($query) use ($value) {
            $search = "%{$value}%";

            $query
                ->where('media.filename', 'ILIKE', $search)
                ->orWhereRaw(
                    $this->normalizedColumn('media.alt') . ' ILIKE ?',
                    [$search]
                );;
        });
    }

    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function filterCustomtype(Builder $builder, $value): Builder
    {
        $types = [
            'image' => [
                MediaAlias::TYPE_IMAGE,
                MediaAlias::TYPE_IMAGE_VECTOR,
            ],
            'pdf' => [
                MediaAlias::TYPE_PDF,
            ],
            'audio' => [
                MediaAlias::TYPE_AUDIO,
            ],
            'video' => [
                MediaAlias::TYPE_VIDEO,
            ],
            'archive' => [
                MediaAlias::TYPE_ARCHIVE,
            ],
            'document' => [
                MediaAlias::TYPE_DOCUMENT,
            ],
            'spreadsheet' => [
                MediaAlias::TYPE_SPREADSHEET,
            ],
            'presentation' => [
                MediaAlias::TYPE_PRESENTATION,
            ],
        ];

        if (!isset($types[$value])) {
            return $builder;
        }

        return $builder->whereIn('media.aggregate_type', $types[$value]);
    }

    /**
     * @param Builder $builder
     * @param $value
     * @return Builder
     */
    public function filterCustomsort(Builder $builder, $value): Builder
    {
        $sorts = [
            'size_desc' => ['media.size', 'desc'],
            'created_at_desc' => ['media.id', 'desc'],
        ];

        if (!isset($sorts[$value])) {
            return $builder;
        }

        [$column, $direction] = $sorts[$value];

        return $builder
            ->orderBy($column, $direction);
    }
}
