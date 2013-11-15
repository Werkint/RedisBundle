<?php
namespace Werkint\Bundle\RedisBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * WerkintRedisExtension.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class WerkintRedisExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(
        array $configs,
        ContainerBuilder $container
    ) {
        $processor = new Processor();
        $config = $processor->processConfiguration(
            new Configuration($this->getAlias()),
            $configs
        );
        // TODO: restructure
        $container->setParameter(
            $this->getAlias(), $config
        );
        $container->setParameter(
            $this->getAlias() . '_connection',
            $config['connection']
        );
        $container->setParameter(
            $this->getAlias() . '_project',
            $config['project']
        );
        $prefix = $config['project'] . '_';
        $prefix .= $container->getParameter('kernel.environment');
        $container->setParameter(
            $this->getAlias() . '_prefix', $prefix
        );
        $container->setParameter(
            $this->getAlias() . '_session',
            $config['session']
        );
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');
    }

}
