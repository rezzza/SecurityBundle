<?php

namespace Rezzza\SecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 *
 * @uses ConfigurationInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $tb       = new TreeBuilder();
        $rootNode = $tb->root('rezzza_security');

        $rootNode
            ->children()
                ->arrayNode('request_obfuscator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('firewalls')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('algorithm')
                                ->defaultValue('SHA1')
                            ->end()
                            ->scalarNode('secret')
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->booleanNode('replay_protection')
                                ->defaultValue(true)
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ;

        return $tb;
    }
}
