<?php

namespace Lavary\Menus\Configuration;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Definition implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('menu');

        $rootNode
            ->children()
                            
                ->booleanNode('auto_activate')->end()
                ->booleanNode('current_affect_parents')->end()
                ->booleanNode('cascade_data')->end()
                ->enumNode('active_element')
                    ->values(['item', 'link'])
                ->end()
                ->variableNode('active_attributes')->end()
                            
            ->end()
        ;

        return $treeBuilder;
    }
}
