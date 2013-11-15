<?php
namespace Werkint\Bundle\RedisBundle\Tests\DependencyInjectio\Service;

use Werkint\Bundle\RedisBundle\Service\Redis;

/**
 * RedisTest.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class RedisTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $obj = new Redis('host', 123, 'pass');
        $this->assertFalse($obj->isConnected());
    }

}
