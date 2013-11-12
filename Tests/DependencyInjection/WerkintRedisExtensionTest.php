<?php
namespace Werkint\Bundle\RedisBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Werkint\Bundle\RedisBundle\DependencyInjection\WerkintRedisExtension;

/**
 * WerkintRedisExtensionTest.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class WerkintRedisExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testRequiredConfig()
    {
        $this->loadContainer([]);
    }

    public function testConfig()
    {
        $container = $this->loadContainer(['project' => 'test123']);

        $this->assertTrue($container->hasParameter('werkint_redis_project'));
        $this->assertEquals('test123', $container->getParameter('werkint_redis_project'));
    }

    public function testPrefix()
    {
        $container = $this->loadContainer(['project' => 'test123']);

        $this->assertEquals('test123_foo', $container->getParameter('werkint_redis_prefix'));
    }

    public function testServices()
    {
        $container = $this->loadContainer(['project' => 'test123']);

        $this->assertTrue(
            $container->hasDefinition('werkint.redis.service'),
            'Redis service is loaded'
        );
    }

    /**
     * @param array $config
     * @return ContainerBuilder
     */
    protected function loadContainer(array $config)
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.environment', 'foo');
        $loader = new WerkintRedisExtension();
        $loader->load([$config], $container);
        return $container;
    }
}
