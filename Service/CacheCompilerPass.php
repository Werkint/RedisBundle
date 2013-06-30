<?php
namespace Werkint\Bundle\RedisBundle\Service;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class CacheCompilerPass implements CompilerPassInterface
{
    const PROVIDER_PREFIX = 'werkint.redis.ns';
    const SERVICE_NAME = 'werkint.redis.service';

    public function process(ContainerBuilder $container)
    {
        // Проходимся по списку
        $list = $container->findTaggedServiceIds('werkint.redis.cacher');
        foreach ($list as $id => $attributes) {
            $ns = isset($attributes[0]['ns']) ? $attributes[0]['ns'] : '_root';
            $definition = new DefinitionDecorator('werkint.redis.provider');
            $definition->addTag('werkint.redis.cacheservice', ['ns' => $ns]);
            $definition->setPublic(false);
            $container->setDefinition(
                static::PROVIDER_PREFIX . '.' . $attributes[0]['ns'],
                $definition
            );
        }
        // Проходимся по списку
        $list = $container->findTaggedServiceIds('werkint.redis.cacheservice');
        foreach ($list as $id => $attributes) {
            $definition = $container->getDefinition($id);
            if (!isset($attributes[0]['ns'])) {
                throw new \Exception('Wrong namespace in ' . $id);
            }
            $namespace = $container->getParameter('werkint_redis_prefix') . '_' . $attributes[0]['ns'];
            $definition->addMethodCall(
                'setNamespace', [$namespace]
            );
            $definition->addMethodCall(
                'setRedis', [new Reference(static::SERVICE_NAME)]
            );
        }
    }
}