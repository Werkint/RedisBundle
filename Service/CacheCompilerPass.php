<?php
namespace Werkint\MemcachedBundle\Service;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\DefinitionDecorator,
    Symfony\Component\DependencyInjection\Reference;

class CacheCompilerPass implements CompilerPassInterface
{
    const PROVIDER_PREFIX = 'werkint.memcached.ns';
    const SERVICE_NAME = 'werkint.memcached.service';

    public function process(ContainerBuilder $container)
    {
        // Проходимся по списку
        $list = $container->findTaggedServiceIds('werkint.memcached.cacher');
        foreach ($list as $id => $attributes) {
            if (!isset($attributes[0]['ns'])) {
                throw new \Exception('Wrong namespace in ' . $id);
            }
            $namespace = $container->getParameter('werkint_memcached_prefix') . '_' . $attributes[0]['ns'];
            $definition = new DefinitionDecorator('werkint.memcached.provider');
            $definition->addMethodCall(
                'setNamespace', [$namespace]
            );
            $definition->addMethodCall(
                'setMemcached', [new Reference(static::SERVICE_NAME)]
            );
            $definition->setPublic(false);
            $container->setDefinition(
                static::PROVIDER_PREFIX . '.' . $attributes[0]['ns'],
                $definition
            );
        }
    }
}