<?php
namespace Werkint\Bundle\RedisBundle\Tests\Currency;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Werkint\Bundle\RedisBundle\WerkintRedisBundle;

/**
 * WerkintRedisBundleTest.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class WerkintRedisBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testPasses()
    {
        $containerBuilderMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $class = 'Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface';
        $containerBuilderMock->expects($this->exactly(1))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf($class))
            ->will($this->returnValue(true));
        $obj = new WerkintRedisBundle();
        $obj->build($containerBuilderMock);
    }

}
