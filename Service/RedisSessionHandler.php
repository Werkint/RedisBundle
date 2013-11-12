<?php
namespace Werkint\Bundle\RedisBundle\Service;

/**
 * Redis based session storage
 *
 * @author Justin Rainbow <justin.rainbow@gmail.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Henrik Westphal <henrik.westphal@gmail.com>
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class RedisSessionHandler implements
    \SessionHandlerInterface
{
    protected $redis;
    protected $ttl;
    protected $prefix;

    /**
     * Redis session storage constructor
     *
     * @param Redis  $redis   Redis database connection
     * @param array  $options Session options
     * @param string $prefix  Prefix to use when writing session data
     */
    public function __construct(
        Redis $redis,
        array $options = [],
        $prefix = 'session'
    ) {
        $this->redis = $redis;
        $this->ttl = isset($options['cookie_lifetime']) ? (int)$options['cookie_lifetime'] : 0;
        $this->prefix = $prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function open(
        $savePath,
        $sessionName
    ) {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        return $this->redis->get($this->getRedisKey($sessionId)) ? : '';
    }

    /**
     * {@inheritdoc}
     */
    public function write(
        $sessionId,
        $data
    ) {
        if (0 < $this->ttl) {
            $this->redis->setex(
                $this->getRedisKey($sessionId),
                $this->ttl,
                $data
            );
        } else {
            $this->redis->set(
                $this->getRedisKey($sessionId),
                $data
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        $this->redis->del($this->getRedisKey($sessionId));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($lifetime)
    {
        return true;
    }

    /**
     * Change the default TTL
     *
     * @param int $ttl
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;
    }

    /**
     * Prepends the session ID with a user-defined prefix (if any).
     *
     * @param string $sessionId session ID
     *
     * @return string prefixed session ID
     */
    protected function getRedisKey($sessionId)
    {
        if (empty($this->prefix)) {
            return $sessionId;
        }

        return $this->prefix . ':' . $sessionId;
    }

}
