<?php
namespace Werkint\Bundle\RedisBundle\Tests\DependencyInjection\Compiler;

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
        $renderersPass = new CacheProviderPass();

        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $this->assertNull($renderersPass->process(
            $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder')
        ));
    }

}
