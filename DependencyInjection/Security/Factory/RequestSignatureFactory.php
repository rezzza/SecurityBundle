<?php

namespace Rezzza\SecurityBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

class RequestSignatureFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $signatureQueryParametersId = $this->createSignatureQueryParameters($container, $id, $config);
        $signatureConfigId = $this->createSignatureConfig($container, $id, $config);
        $replayProtectionId = $this->createReplayProtection($container, $id, $config);
        $providerId = 'security.authentication.provider.request_signature.'.$id;

        $container
            ->setDefinition($providerId, $this->createDefinition('rezzza.security.request_signature.provider'))
            ->addArgument(new Reference($signatureConfigId))
            ->addArgument(new Reference($replayProtectionId))
        ;

        $listenerId = 'security.authentication.listener.request_signature.'.$id;
        $listener = $container
            ->setDefinition($listenerId, $this->createDefinition('rezzza.security.request_signature.listener'))
            ->replaceArgument(2, new Reference($signatureQueryParametersId))
            ->replaceArgument(3, $config['ignore'])
        ;

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

    public function createSignatureConfig($container, $id, $config)
    {
        $signatureConfigId = 'rezzza.security.request_signature.signature_config.'.$id;
        $container
            ->setDefinition($signatureConfigId, $this->createDefinition('rezzza.security.request_signature.signature_config'))
            ->addArgument($config['replay_protection']['enabled'])
            ->addArgument($config['algorithm'])
            ->addArgument($config['secret'])
        ;

        return $signatureConfigId;
    }

    public function createSignatureQueryParameters($container, $id, $config)
    {
        $signatureQueryParametersId = 'rezzza.security.request_signature.signature_query_parameters.'.$id;
        $container
            ->setDefinition($signatureQueryParametersId, $this->createDefinition('rezzza.security.request_signature.signature_query_parameters'))
            ->addArgument($config['parameter'])
            ->addArgument($config['replay_protection']['parameter'])
        ;

        return $signatureQueryParametersId;
    }

    public function createReplayProtection($container, $id, $config)
    {
        $replayProtectionId = 'rezzza.security.request_signature.replay_protection.'.$id;
        $container
            ->setDefinition($replayProtectionId, $this->createDefinition('rezzza.security.request_signature.replay_protection'))
            ->addArgument($config['replay_protection']['enabled'])
            ->addArgument($config['replay_protection']['lifetime'])
        ;

        return $replayProtectionId;
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

    /**
     * @param $serviceId
     * @return ChildDefinition|DefinitionDecorator
     */
    private function createDefinition($serviceId)
    {
        if (class_exists('\Symfony\Component\DependencyInjection\ChildDefinition')) {
            return new \Symfony\Component\DependencyInjection\ChildDefinition($serviceId);
        } else {
            return new \Symfony\Component\DependencyInjection\DefinitionDecorator($serviceId);
        }
    }
}
