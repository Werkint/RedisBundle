<?php
namespace Werkint\Bundle\RedisBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class WerkintRedisExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(
            new Configuration($this->getAlias()), $configs
        );
        $container->setParameter(
            $this->getAlias(), $config
        );
        $container->setParameter(
            $this->getAlias() . '_host', $config['host']
        );
        $container->setParameter(
            $this->getAlias() . '_port', $config['port']
        );
        $container->setParameter(
            $this->getAlias() . '_prefix', $config['prefix']
        );
        $container->setParameter(
            $this->getAlias() . '_session_prefix', $config['session']['prefix']
        );
        $container->setParameter(
            $this->getAlias() . '_session_expire', $config['session']['expire']
        );
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yml');
    }

    public function getAlias()
    {
        return 'werkint_redis';
    }
}
