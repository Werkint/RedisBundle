<?php
namespace Werkint\Bundle\RedisBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->alias)->children();

        $rootNode->scalarNode('host')->defaultValue('127.0.0.1')->end();
        $rootNode->scalarNode('port')->defaultValue('6379')->end();
        $rootNode->scalarNode('pass')->end();
        $rootNode->scalarNode('project')->end();

        $rootNode->arrayNode('session')
            ->children()
            ->scalarNode('prefix')->end()
            ->scalarNode('expire')->defaultValue('3600')->end()
            ->end()
            ->end();

        $rootNode->end();
        return $treeBuilder;
    }
}