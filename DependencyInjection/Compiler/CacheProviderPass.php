<?php
namespace Werkint\Bundle\RedisBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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
    const CLASS_TAG = 'werkint.redis.cache';
    const CLASS_DOCTRINE = 'werkint.redis.doctrinecacher';
    // Prefix for all cache services
    const PROVIDER_PREFIX = 'werkint.redis.ns.';
    // Basic cache services
    const PROVIDER_CLASS = 'werkint.redis.provider';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(static::CLASS_SRV)) {
            return false;
        }
        $project = $container->getParameter('werkint_redis_project');
        $prefix = $container->getParameter('werkint_redis_prefix');

        // Doctrine
        $cache = $container->getDefinition(static::CLASS_DOCTRINE);
        $this->setRedis($cache, $prefix . '_doctrine');

        // Go through list
        $list = $container->findTaggedServiceIds(static::CLASS_TAG);
        foreach ($list as $id => $attributes) {
            $definition = $container->getDefinition($id);
            foreach ($attributes as $a) {
                $scope = isset($a['scope']) ? $a['scope'] : 'project';
                if (!in_array($scope, ['root', 'project'])) {
                    throw new \InvalidArgumentException('Wrong service scope of ' . $id);
                }

                $ns = static::PROVIDER_PREFIX;
                $cacheNs = $prefix;
                if ($scope == 'root') {
                    if (!isset($a['ns'])) {
                        throw new \InvalidArgumentException('Service namespace not defined of ' . $id);
                    }
                    $ns .= '_root.' . $a['ns'];
                    $cacheNs .= '_root.' . $a['ns'];
                } else {
                    $ns .= $project;
                    if (isset($a['ns'])) {
                        $ns .= '.' . $a['ns'];
                        $cacheNs .= '.' . $a['ns'];
                    }
                }

                if ($container->hasDefinition($ns)) {
                    $cache = $container->getDefinition($ns);
                } else {
                    $cache = new DefinitionDecorator(static::PROVIDER_CLASS);
                    $cache->setPublic(false);
                    $this->setRedis($cache, $cacheNs);
                    $container->setDefinition($ns, $cache);
                }
                $definition->addArgument($cache);
            }
        }
    }

    /**
     * @param Definition $cache
     * @param string     $cacheNs
     */
    protected function setRedis(
        Definition $cache,
        $cacheNs
    ) {
        $cache->addMethodCall(
            'setNamespace', [$cacheNs]
        );
        $cache->addMethodCall(
            'setRedis', [new Reference(static::CLASS_SRV)]
        );
    }

}
