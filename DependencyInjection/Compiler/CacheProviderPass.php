<?php
namespace Werkint\Bundle\RedisBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * CacheProviderPass.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class CacheProviderPass implements
    CompilerPassInterface
{
    const CLASS_SRV = 'werkint.redis.service';
    const CLASS_TAG1 = 'werkint.redis.cache';
    const CLASS_TAG2 = 'werkint.redis.cacher';
    const CLASS_TAG_SRV = 'werkint.redis.cacheservice';
    const PROVIDER_CLASS = 'werkint.redis.provider';
    const PROVIDER_PREFIX = 'werkint.redis.ns';

    /**
     * @param ContainerBuilder $container
     */
    protected function processServices(
        ContainerBuilder $container
    ) {
        $nsprefix = $container->getParameter('werkint_redis_prefix') . '_';
        $list = $container->findTaggedServiceIds(static::CLASS_TAG2);
        foreach ($list as $attributes) {
            $ns = isset($attributes[0]['ns']) ? $attributes[0]['ns'] : '_root';
            $definition = new DefinitionDecorator(static::PROVIDER_CLASS);
            $definition->addTag(static::CLASS_TAG_SRV, ['ns' => $nsprefix . $ns]);
            $definition->setPublic(false);
            $container->setDefinition(
                static::PROVIDER_PREFIX . '.' . $ns,
                $definition
            );
        }
        $list = $container->findTaggedServiceIds(static::CLASS_TAG1);
        foreach ($list as $attributes) {
            $ns = isset($attributes[0]['ns']) ? $attributes[0]['ns'] : '_root';
            $definition = new DefinitionDecorator(static::PROVIDER_CLASS);
            $definition->addTag(static::CLASS_TAG_SRV, ['ns' => $nsprefix . '_' . $ns]);
            $definition->setPublic(false);
            $container->setDefinition(
                $container->getParameter('werkint_redis_project') . '.cache.' . $ns,
                $definition
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(static::CLASS_SRV)) {
            return;
        }

        // Go through list
        $this->processServices($container);

        // Go through list
        $list = $container->findTaggedServiceIds(static::CLASS_TAG_SRV);
        foreach ($list as $id => $attributes) {
            $definition = $container->getDefinition($id);
            if (!isset($attributes[0]['ns'])) {
                throw new \Exception('Wrong namespace in ' . $id);
            }
            $definition->addMethodCall(
                'setNamespace', [$attributes[0]['ns']]
            );
            $definition->addMethodCall(
                'setRedis', [new Reference(static::CLASS_SRV)]
            );
        }
    }

}
