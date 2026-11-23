<?php

namespace App\Models\V1\Banner;

use App\Enums\V1\Banner\BannerPlacement;
use App\ModelFilters\V1\Banner\BannersFilter;
use App\Policies\V1\Banner\BannerPolicy;
use eloquentFilter\QueryFilter\ModelFilters\Filterable;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[UsePolicy(BannerPolicy::class)]
class Banner extends Model
{
    use Filterable, BannersFilter, Mediable;

    protected $fillable = [
        'title',
        'link',
        'placement',
        'rel',
        'status',
        'user_id',
        'start_at',
        'end_at',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'immutable_datetime',
            'end_at' => 'immutable_datetime',
        ];
    }

    private static array $whiteListFilter = [
        'status',
        'rel',
        'placement'
    ];

    protected static function booted(): void
    {
        static::saved(function (Banner $banner): void {
            self::replacePlacement($banner);
        });
    }

    protected function startAt(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $value === null
                ? null
                : CarbonImmutable::parse($value)->utc()
        );
    }

    protected function endAt(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $value === null
                ? null
                : CarbonImmutable::parse($value)->utc()
        );
    }

    private static function replacePlacement(Banner $banner): void
    {
        $placement = $banner->getAttribute('placement');

        if ($placement instanceof BannerPlacement) {
            $placement = $placement->value;
        }

        if (!$placement || $placement === BannerPlacement::NONE->value) {
            return;
        }

        static::query()
            ->where('placement', $placement)
            ->where($banner->getKeyName(), '!=', $banner->getKey())
            ->update([
                'placement' => BannerPlacement::NONE->value,
            ]);
    }
}
