<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class RedisHelper
{
    protected $connection;

    public function __construct($connection = 'default')
    {
        $this->connection = $connection;
    }

    /**
     * Get value from Redis
     */
    public static function get(string $key)
    {
        try {
            return Redis::get($key);
        } catch (\Exception $e) {
            Log::error("Redis GET error for key {$key}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Set value to Redis (with optional expiration in seconds)
     */
    public static function set(string $key, $value, $ttl = null): bool
    {
        try {
            if (Redis::exists($key) > 0) {
                Log::warning("key already found, your replace that {$key}");
            }
            Redis::setex($key, $ttl ?? env('REDIS_TTL'), $value);

            return true;
        } catch (\Exception $e) {
            dd($e);
            Log::error("Redis SET error for key {$key}: ".$e->getMessage());

            return false;
        }
    }

    public function getAllKeys($pattern = '*')
    {
        $keys = [];
        $cursor = null;

        do {
            $result = Redis::connection($this->connection)->scan($cursor, 'MATCH', $pattern, 'COUNT', 100);
            if ($result) {
                [$cursor, $fetchedKeys] = $result;
                $keys = array_merge($keys, $fetchedKeys);
            }
        } while ($cursor != 0);

        return $keys;
    }

    /**
     * Delete a key from Redis
     */
    public static function del(string $key): bool
    {
        try {
            Redis::del($key);

            return true;
        } catch (\Exception $e) {
            Log::error("Redis DEL error for key {$key}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Check if a key exists
     */
    public static function exists(string $key): bool
    {
        try {
            return Redis::exists($key) > 0;
        } catch (\Exception $e) {
            Log::error("Redis EXISTS error for key {$key}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Increment a key
     */
    public static function increment(string $key, int $amount = 1)
    {
        try {
            return Redis::incrby($key, $amount);
        } catch (\Exception $e) {
            Log::error("Redis INCR error for key {$key}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Decrement a key
     */
    public static function decrement(string $key, int $amount = 1)
    {
        try {
            return Redis::decrby($key, $amount);
        } catch (\Exception $e) {
            Log::error("Redis DECR error for key {$key}: ".$e->getMessage());

            return null;
        }
    }
}
