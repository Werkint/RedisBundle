<?php
namespace Werkint\MemcachedBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Werkint\MemcachedBundle\Service\CacheCompilerPass;

class WerkintMemcachedBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        // Кеш
        $container->addCompilerPass(new CacheCompilerPass);
    }
}
