<?php

namespace App\Traits\V1\Core\Cache;

use Illuminate\Support\Facades\Redis;

trait ClearsRedisCache
{
    /**
     * @param string $pattern
     * @return void
     */
    public function clearCache(string $pattern): void
    {
        $redisPrefix = config('database.redis.options.prefix', '');

        $keys = Redis::keys($pattern);

        foreach ($keys as $key) {
            if (
                $redisPrefix !== '' &&
                str_starts_with($key, $redisPrefix)
            ) {
                $key = substr($key, strlen($redisPrefix));
            }

            Redis::del($key);
        }
    }
}
