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

        $rootNode->scalarNode('host')->end();
        $rootNode->scalarNode('port')->end();
        $rootNode->scalarNode('pass')->end();
        $rootNode->scalarNode('prefix')->end();

        $rootNode->arrayNode('session')
            ->children()
            ->scalarNode('prefix')->end()
            ->scalarNode('expire')->end()
            ->end()
            ->end();

        $rootNode->end();
        return $treeBuilder;
    }
}