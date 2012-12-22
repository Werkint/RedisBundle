<?php
namespace Werkint\MemcachedBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\YamlFileLoader,
    Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\Config\Definition\Processor,
    Symfony\Component\Config\FileLocator;

class WerkintMemcachedExtension extends Extension
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
        return 'werkint_memcached';
    }
}
