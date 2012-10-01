<?php

namespace Rezzza\SecurityBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Rezzza\SecurityBundle\Security\Firewall\RequestSignatureConfiguration;

class RequestSignatureFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $entryPointId = $this->createEntryPoint($container, $id, $config, $defaultEntryPoint);
        $providerId = 'security.authentication.provider.request_signature.'.$id;

        $container
            ->setDefinition($providerId, new DefinitionDecorator('rezzza.security.request_signature.provider'))
            ->addArgument(new Reference($entryPointId))
            ;

        $listenerId = 'security.authentication.listener.request_signature.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('rezzza.security.request_signature.listener'))
            ->addArgument(new Reference($entryPointId));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'request_signature';
    }

    public function createEntryPoint($container, $id, $config, $defaultEntryPoint)
    {
        if (null !== $defaultEntryPoint) {
            return $defaultEntryPoint;
        }

        $entryPointId = 'rezzza.security.request_signature.entry_point.'.$id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('rezzza.security.request_signature.entry_point'))
            ->addArgument($config)
            ;

        return $entryPointId;
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $node->children()
            ->scalarNode('algorithm')->defaultValue('SHA1')->cannotBeEmpty()->end()
            ->scalarNode('secret')->isRequired()->cannotBeEmpty()->end()
            ->booleanNode('ignore')->defaultFalse()->end()
            ->scalarNode('parameter')->defaultValue('_signature')->cannotBeEmpty()->end()
            ->arrayNode('replay_protection')
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('enabled')->defaultTrue()->end()
                    ->scalarNode('lifetime')->defaultValue(600)->end()
                    ->scalarNode('parameter')->defaultValue('_signature_time')->cannotBeEmpty()->end()
                ->end()
            ->end()
            ;

    }
}
