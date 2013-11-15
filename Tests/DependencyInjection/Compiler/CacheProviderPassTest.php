<?php
namespace Werkint\Bundle\RedisBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Werkint\Bundle\RedisBundle\DependencyInjection\Compiler\CacheProviderPass;

/**
 * CacheProviderPassTest.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class CacheProviderPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessWithoutProviderDefinition()
    {
        $pass = new CacheProviderPass();
        $container = $this->getContainer(true);

        $this->assertFalse($pass->process($container));
    }

    /**
     * @depends testProcessWithoutProviderDefinition
     * @expectedException \InvalidArgumentException
     */
    public function testWrongScope()
    {
        $pass = new CacheProviderPass();
        $container = $this->getContainer();

        $srv = new Definition();
        $srv->addTag(CacheProviderPass::CLASS_TAG, [
            'scope' => 'foowrongscope',
        ]);
        $container->setDefinition('foosrv', $srv);

        $pass->process($container);
    }

    /**
     * @depends testWrongScope
     * @expectedException \InvalidArgumentException
     */
    public function testNoRootNs()
    {
        $pass = new CacheProviderPass();
        $container = $this->getContainer();

        $srv = new Definition();
        $srv->addTag(CacheProviderPass::CLASS_TAG, [
            'scope' => 'root',
        ]);
        $container->setDefinition('foosrv', $srv);

        $pass->process($container);
    }

    /**
     * @depends testNoRootNs
     */
    public function testPasses()
    {
        $pass = new CacheProviderPass();
        $container = $this->getContainer();

        $srv = new Definition();
        $srv->addTag(CacheProviderPass::CLASS_TAG, [
            'scope' => 'root',
            'ns'    => 'test1',
        ]);
        $srv->addTag(CacheProviderPass::CLASS_TAG, [
            'scope' => 'project',
            'ns'    => 'test2',
        ]);
        $srv->addTag(CacheProviderPass::CLASS_TAG, [
            'scope' => 'project',
        ]);
        $container->setDefinition('foosrv', $srv);

        $this->assertNull($pass->process($container));
        $ns = CacheProviderPass::PROVIDER_PREFIX;
        $this->assertEquals(3, count($srv->getArguments()));
        $this->assertTrue($container->hasDefinition($ns . '_root.test1'));
        $this->assertTrue($container->hasDefinition($ns . 'fooproject'));
        $this->assertTrue($container->hasDefinition($ns . 'fooproject.test2'));
    }

    /**
     * @depends testPasses
     */
    public function testRedisNamespace()
    {
        $pass = new CacheProviderPass();
        $container = $this->getContainer();

        $srv = new Definition();
        $srv->addTag(CacheProviderPass::CLASS_TAG, [
            'scope' => 'project',
        ]);
        $srv->addTag(CacheProviderPass::CLASS_TAG, [
            'scope' => 'project',
        ]);
        $srv->setSynthetic(true);
        $container->setDefinition('foosrv', $srv);

        $this->assertNull($pass->process($container));
        $args = $srv->getArguments();
        $ns = CacheProviderPass::PROVIDER_PREFIX;
        $this->assertEquals(2, count($args));
        $this->assertTrue($args[0] === $args[1]);
        $def = $container->getDefinition($ns . 'fooproject');

        $ok = 0;
        foreach ($def->getMethodCalls() as $arr) {
            if ($arr[0] == 'setNamespace') {
                $this->assertEquals('fooproject_testenv', $arr[1][0]);
                $ok++;
            }
        }
        $this->assertEquals(1, $ok, 'Namespace for redis not set');
    }

    /**
     * @param bool $nosrv
     * @return ContainerBuilder
     */
    protected function getContainer($nosrv = false)
    {
        $container = new ContainerBuilder();
        $container->setParameter('werkint_redis_project', 'fooproject');
        $container->setParameter('werkint_redis_prefix', 'fooproject_testenv');
        if (!$nosrv) {
            $srv = new Definition();
            $container->setDefinition(CacheProviderPass::CLASS_SRV, $srv);
        }
        $srv = new Definition();
        $container->setDefinition('werkint.redis.provider', $srv);
        return $container;
    }

}
