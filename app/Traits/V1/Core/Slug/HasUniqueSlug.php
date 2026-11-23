<?php

namespace App\Traits\V1\Core\Slug;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 * @phpstan-require-extends Model
 */
trait HasUniqueSlug
{
    protected static function bootHasUniqueSlug(): void
    {
        static::saving(function (self $model): void {
            $model->ensureUniqueSlugValue();
        });
    }

    protected function ensureUniqueSlugValue(): void
    {
        $sourceColumn = $this->uniqueSlugSourceColumn();
        $slugColumn = $this->uniqueSlugColumn();

        if (
            !$this->isDirty($sourceColumn) &&
            !empty($this->getAttribute($slugColumn))
        ) {
            return;
        }

        $baseSlug = $this->getAttribute($sourceColumn)
                |> trim(...)
                |> (fn($x) => preg_replace('/[\p{Z}\s]+/u', '-', $x))
                |> (fn($x) => trim($x, '-'));

        $slug = $baseSlug;
        $counter = 2;

        while (true) {
            $query = $this->newQuery()
                ->where($slugColumn, $slug);

            if ($this->exists) {
                $query->where(
                    $this->getKeyName(),
                    '!=',
                    $this->getKey()
                );
            }

            if (!$query->exists()) {
                break;
            }

            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $this->setAttribute($slugColumn, $slug);
    }

    protected function uniqueSlugSourceColumn(): string
    {
        return 'title';
    }

    protected function uniqueSlugColumn(): string
    {
        return 'slug';
    }
}
