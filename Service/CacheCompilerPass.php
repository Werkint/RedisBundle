<?php
namespace Werkint\Bundle\RedisBundle\Service;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * CacheCompilerPass.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class CacheCompilerPass implements
    CompilerPassInterface
{
    const PROVIDER_PREFIX = 'werkint.redis.ns';
    const SERVICE_NAME = 'werkint.redis.service';

    protected function processServices(ContainerBuilder $container)
    {
        $nsprefix = $container->getParameter('werkint_redis_prefix') . '_';
        $list = $container->findTaggedServiceIds('werkint.redis.cacher');
        foreach ($list as $attributes) {
            $ns = isset($attributes[0]['ns']) ? $attributes[0]['ns'] : '_root';
            $definition = new DefinitionDecorator('werkint.redis.provider');
            $definition->addTag('werkint.redis.cacheservice', ['ns' => $nsprefix . $ns]);
            $definition->setPublic(false);
            $container->setDefinition(
                static::PROVIDER_PREFIX . '.' . $ns,
                $definition
            );
        }
        $list = $container->findTaggedServiceIds('werkint.redis.cache');
        foreach ($list as $attributes) {
            $ns = isset($attributes[0]['ns']) ? $attributes[0]['ns'] : '_root';
            $definition = new DefinitionDecorator('werkint.redis.provider');
            $definition->addTag('werkint.redis.cacheservice', ['ns' => $nsprefix . '_' . $ns]);
            $definition->setPublic(false);
            $container->setDefinition(
                $container->getParameter('werkint_redis_project') . '.cache.' . $ns,
                $definition
            );
        }
    }

    public function process(ContainerBuilder $container)
    {
        // Проходимся по списку
        $this->processServices($container);

        // Проходимся по списку
        $list = $container->findTaggedServiceIds('werkint.redis.cacheservice');
        foreach ($list as $id => $attributes) {
            $definition = $container->getDefinition($id);
            if (!isset($attributes[0]['ns'])) {
                throw new \Exception('Wrong namespace in ' . $id);
            }
            $definition->addMethodCall(
                'setNamespace', [$attributes[0]['ns']]
            );
            $definition->addMethodCall(
                'setRedis', [new Reference(static::SERVICE_NAME)]
            );
        }
    }
}