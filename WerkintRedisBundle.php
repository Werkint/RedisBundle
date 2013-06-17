<?php
namespace Werkint\Bundle\RedisBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Werkint\Bundle\RedisBundle\Service\CacheCompilerPass;

class WerkintRedisBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        // Кеш
        $container->addCompilerPass(new CacheCompilerPass);
    }
}
