<?php
namespace Werkint\Bundle\MemcachedBundle\Service;

use Doctrine\Common\Cache\CacheProvider;

/**
 * Redis cache provider.
 *
 * @link   www.doctrine-project.org
 * @since  2.2
 * @author Osman Ungur <osmanungur@gmail.com>
 */
class RedisCache extends CacheProvider
{
    /**
     * @var Redis|null
     */
    private $redis;

    /**
     * Sets the redis instance to use.
     *
     * @param Redis $redis
     *
     * @return void
     */
    public function setRedis(Redis $redis)
    {
        $redis->setOption(Redis::OPT_SERIALIZER, $this->getSerializerValue());
        $this->redis = $redis;
    }

    /**
     * Gets the redis instance used by the cache.
     *
     * @return Redis|null
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
        return $this->redis->get($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return $this->redis->exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        if ($lifeTime > 0) {
            return $this->redis->setex($id, $lifeTime, $data);
        }
        return $this->redis->set($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return $this->redis->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return $this->redis->flushDB();
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        $info = $this->redis->info();
        return array(
            Cache::STATS_HITS   => false,
            Cache::STATS_MISSES => false,
            Cache::STATS_UPTIME => $info['uptime_in_seconds'],
            Cache::STATS_MEMORY_USAGE      => $info['used_memory'],
            Cache::STATS_MEMORY_AVAILABLE  => false
        );
    }

    /**
     * Returns the serializer constant to use. If Redis is compiled with
     * igbinary support, that is used. Otherwise the default PHP serializer is
     * used.
     *
     * @return integer One of the Redis::SERIALIZER_* constants
     */
    protected function getSerializerValue()
    {
        return defined('Redis::SERIALIZER_IGBINARY') ? Redis::SERIALIZER_IGBINARY : Redis::SERIALIZER_PHP;
    }
}
