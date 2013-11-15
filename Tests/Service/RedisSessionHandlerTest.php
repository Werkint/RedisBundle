<?php
namespace Werkint\Bundle\RedisBundle\Tests\DependencyInjectio\Service;

use Werkint\Bundle\RedisBundle\Service\RedisSessionHandler;

/**
 * RedisSessionHandlerTest.
 *
 * @author Henrik Westphal <henrik.westphal@gmail.com>
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class RedisSessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $redis;

    protected function setUp()
    {
        $this->redis = $this->getMock('\Predis\Client', array('get', 'set', 'setex', 'del'));
    }

    protected function tearDown()
    {
        unset($this->redis);
    }

    public function testOpen()
    {
        $handler = new RedisSessionHandler($this->redis, [], 'session');
        $this->assertTrue($handler->open('', ''));
        $this->assertTrue($handler->close());
        $this->assertTrue($handler->gc(0));
    }

    public function testSessionReadingNoPrefix()
    {
        $this->redis
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('_symfony'));

        $handler = new RedisSessionHandler($this->redis, [], null);
        $handler->read('_symfony');
    }

    public function testSessionReading()
    {
        $this->redis
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('sessiontest:_symfony'));

        $handler = new RedisSessionHandler($this->redis, [], 'sessiontest');
        $handler->read('_symfony');
    }

    public function testDeletingSessionData()
    {
        $this->redis
            ->expects($this->once())
            ->method('del')
            ->with($this->equalTo('session:_symfony'));

        $handler = new RedisSessionHandler($this->redis, [], 'session');
        $handler->destroy('_symfony');
    }

    public function testWritingSessionDataWithNoExpiration()
    {
        $this->redis
            ->expects($this->once())
            ->method('set')
            ->with($this->equalTo('session:_symfony'), $this->equalTo('some data'));

        $handler = new RedisSessionHandler($this->redis, [], 'session');
        $handler->write('_symfony', 'some data');
    }

    public function testWritingSessionDataWithExpiration()
    {
        $this->redis
            ->expects($this->exactly(3))
            ->method('setex')
            ->with($this->equalTo('session:_symfony'), $this->equalTo(10), $this->equalTo('some data'));

        // Expiration is set by cookie_lifetime option
        $handler = new RedisSessionHandler($this->redis, array('cookie_lifetime' => 10), 'session');
        $handler->write('_symfony', 'some data');

        // Expiration is set with the TTL attribute
        $handler = new RedisSessionHandler($this->redis, [], 'session');
        $handler->setTtl(10);
        $handler->write('_symfony', 'some data');

        // TTL attribute overrides cookie_lifetime option
        $handler = new RedisSessionHandler($this->redis, array('cookie_lifetime' => 20), 'session');
        $handler->setTtl(10);
        $handler->write('_symfony', 'some data');
    }

}
