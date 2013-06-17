<?php
namespace Werkint\Bundle\RedisBundle\Service;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;

/**
 * Redis cache class
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 * @author Henrik Westphal <henrik.westphal@gmail.com>
 */
class RedisCache extends CacheProvider
{
    /**
     * @var Redis
     */
    protected $redis;

    /**
     * Sets the redis instance to use.
     *
     * @param Redis $redis
     */
    public function setRedis($redis)
    {
        $this->redis = $redis;
    }

    /**
     * Returns the redis instance used by the cache.
     *
     * @return Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        $result = $this->redis->get($id);

        return null === $result ? false : unserialize($result);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return (bool)$this->redis->exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = false)
    {
        if (0 < $lifeTime) {
            $result = $this->redis->setex($id, (int)$lifeTime, serialize($data));
        } else {
            $result = $this->redis->set($id, serialize($data));
        }

        return (bool)$result;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return (bool)$this->redis->del($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return (bool)$this->redis->flushdb();
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        $stats = $this->redis->info();

        return [
            Cache::STATS_HITS              => isset($stats['keyspace_hits']) ? $stats['keyspace_hits'] : $stats['Stats']['keyspace_hits'],
            Cache::STATS_MISSES            => isset($stats['keyspace_misses']) ? $stats['keyspace_misses'] : $stats['Stats']['keyspace_misses'],
            Cache::STATS_UPTIME            => isset($stats['uptime_in_seconds']) ? $stats['uptime_in_seconds'] : $stats['Server']['uptime_in_seconds'],
            Cache::STATS_MEMORY_USAGE      => isset($stats['used_memory']) ? $stats['used_memory'] : $stats['Memory']['used_memory'],
            Cache::STATS_MEMORY_AVAILIABLE => null,
        ];
    }
}