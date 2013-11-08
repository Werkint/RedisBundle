<?php
namespace Werkint\Bundle\RedisBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Werkint\Bundle\RedisBundle\Service\CacheCompilerPass;

/**
 * WerkintRedisBundle.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class WerkintRedisBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // Cache
        $container->addCompilerPass(new CacheCompilerPass);
    }
}
