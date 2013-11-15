<?php
namespace Werkint\Bundle\RedisBundle\Tests\DependencyInjectio\Service;

use Doctrine\Common\Cache\Cache;
use Werkint\Bundle\RedisBundle\Service\RedisCache;


/**
 * RedisCacheTest.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class RedisCacheTest extends \PHPUnit_Framework_TestCase
{
    public function testInjection()
    {
        $redis = $this->getMock(
            'Werkint\Bundle\RedisBundle\Service\Redis',
            [], [], '', false
        );

        $obj = new RedisCache();
        $obj->setRedis($redis);
        $this->assertTrue($obj->getRedis() === $redis);
    }

    /**
     * @depends testInjection
     */
    public function testFetchNotExists()
    {
        $redis = $this->getMock('\stdClass', ['get', 'set']);
        $redis->expects($this->exactly(2))
            ->method('get')
            ->with($this->logicalOr(
                $this->equalTo('DoctrineNamespaceCacheKey[]'),
                $this->equalTo('[foovar][foo]')
            ))
            ->will($this->returnCallback(function ($a) {
                return $a === 'DoctrineNamespaceCacheKey[]' ? serialize('foo') : null;
            }));

        $obj = new RedisCache();
        $obj->setRedis($redis);
        $this->assertFalse($obj->fetch('foovar'));
    }

    /**
     * @depends testFetchNotExists
     */
    public function testFetchExists()
    {
        $redis = $this->getMock('\stdClass', ['get', 'set', 'exists']);
        $redis->expects($this->exactly(2))
            ->method('get')
            ->with($this->logicalOr(
                $this->equalTo('DoctrineNamespaceCacheKey[]'),
                $this->equalTo('[foovar][foo]')
            ))
            ->will($this->returnCallback(function ($a) {
                return $a === 'DoctrineNamespaceCacheKey[]' ? serialize('foo') : serialize(['ehehe']);
            }));
        $redis->expects($this->exactly(2))
            ->method('exists')
            ->with($this->logicalOr(
                $this->equalTo('[foovar2][foo]'),
                $this->equalTo('[foovar][foo]')
            ))
            ->will($this->returnCallback(function ($a) {
                return $a === '[foovar2][foo]' ? false : true;
            }));

        $obj = new RedisCache();
        $obj->setRedis($redis);
        $this->assertEquals(['ehehe'], $obj->fetch('foovar'));
        $this->assertTrue($obj->contains('foovar'));
        $this->assertFalse($obj->contains('foovar2'));
    }

    /**
     * @depends testFetchExists
     */
    public function testDelete()
    {
        $redis = $this->getMock('\stdClass', ['get', 'del']);
        $redis->expects($this->exactly(1))
            ->method('get')
            ->with($this->equalTo('DoctrineNamespaceCacheKey[]'))
            ->will($this->returnValue(serialize('foo')));
        $redis->expects($this->exactly(2))
            ->method('del')
            ->with($this->logicalOr(
                $this->equalTo('[foovar2][foo]'),
                $this->equalTo('[foovar1][foo]')
            ))
            ->will($this->returnCallback(function ($a) {
                return $a === '[foovar2][foo]' ? false : true;
            }));

        $obj = new RedisCache();
        $obj->setRedis($redis);
        $this->assertTrue($obj->delete('foovar1'));
        $this->assertFalse($obj->delete('foovar2'));
    }

    /**
     * @depends testFetchExists
     */
    public function testFlush()
    {
        $redis = $this->getMock('\stdClass', ['flushdb']);
        $redis->expects($this->exactly(1))
            ->method('flushdb')
            ->will($this->returnValue(true));

        $obj = new RedisCache();
        $obj->setRedis($redis);
        $this->assertTrue($obj->flushAll());
    }

    /**
     * @depends testFetchExists
     */
    public function testStats1()
    {
        $redis = $this->getMock('\stdClass', ['info']);
        $redis->expects($this->exactly(1))
            ->method('info')
            ->will($this->returnValue([
                'keyspace_hits'     => 1,
                'keyspace_misses'   => 2,
                'uptime_in_seconds' => 3,
                'used_memory'       => 4,
            ]));

        $obj = new RedisCache();
        $obj->setRedis($redis);
        $this->assertEquals([
            Cache::STATS_HITS              => 1,
            Cache::STATS_MISSES            => 2,
            Cache::STATS_UPTIME            => 3,
            Cache::STATS_MEMORY_USAGE      => 4,
            Cache::STATS_MEMORY_AVAILIABLE => null,
        ], $obj->getStats());
    }

    /**
     * @depends testFetchExists
     */
    public function testStats2()
    {
        $redis = $this->getMock('\stdClass', ['info']);
        $redis->expects($this->exactly(1))
            ->method('info')
            ->will($this->returnValue([
                'Stats'  => [
                    'keyspace_hits'   => 1,
                    'keyspace_misses' => 2,
                ],
                'Server' => [
                    'uptime_in_seconds' => 3,
                ],
                'Memory' => [
                    'used_memory' => 4,
                ],
            ]));

        $obj = new RedisCache();
        $obj->setRedis($redis);
        $this->assertEquals([
            Cache::STATS_HITS              => 1,
            Cache::STATS_MISSES            => 2,
            Cache::STATS_UPTIME            => 3,
            Cache::STATS_MEMORY_USAGE      => 4,
            Cache::STATS_MEMORY_AVAILIABLE => null,
        ], $obj->getStats());
    }

    /**
     * @depends testFetchExists
     */
    public function testSave()
    {
        $redis = $this->getMock('\stdClass', ['get', 'setex', 'set']);
        $redis->expects($this->exactly(1))
            ->method('get')
            ->with($this->equalTo('DoctrineNamespaceCacheKey[]'))
            ->will($this->returnValue(serialize('foo')));
        $redis->expects($this->exactly(1))
            ->method('setex')
            ->with(
                $this->equalTo('[foovar][foo]'),
                $this->equalTo(16),
                serialize('data')
            )
            ->will($this->returnValue(true));
        $redis->expects($this->exactly(1))
            ->method('set')
            ->with(
                $this->equalTo('[foovar][foo]'),
                serialize('data')
            )
            ->will($this->returnValue(false));

        $obj = new RedisCache();
        $obj->setRedis($redis);
        $this->assertTrue($obj->save('foovar', 'data', 16));
        $this->assertFalse($obj->save('foovar', 'data'));
    }

}
