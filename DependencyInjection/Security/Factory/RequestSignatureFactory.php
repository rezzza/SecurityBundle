<?php

declare(strict_types=1);

namespace Rezzza\SecurityBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AuthenticatorFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RequestSignatureFactory implements AuthenticatorFactoryInterface
{
    public function createAuthenticator(ContainerBuilder $container, string $firewallName, array $config, string $userProviderId): array|string
    {
        $signatureQueryParametersId = $this->createSignatureQueryParameters($container, $firewallName, $config);
        $signatureConfigId = $this->createSignatureConfig($container, $firewallName, $config);
        $replayProtectionId = $this->createReplayProtection($container, $firewallName, $config);

        $listenerId = 'security.authentication.listener.request_signature.'.$firewallName;
        $listener = $container
            ->setDefinition($listenerId, $this->createDefinition('rezzza.security.request_signature.listener'))
            ->replaceArgument(1, new Reference($signatureQueryParametersId))
            ->replaceArgument(2, $config['ignore'])
            ->replaceArgument(3, new Reference($signatureConfigId))
            ->replaceArgument(4, new Reference($replayProtectionId))
        ;

        return $listenerId;
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey(): string
    {
        return 'request_signature';
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function createSignatureConfig(ContainerBuilder $container, string $firewallName, array $config): string
    {
        $signatureConfigId = 'rezzza.security.request_signature.signature_config.'.$firewallName;
        $container
            ->setDefinition($signatureConfigId, $this->createDefinition('rezzza.security.request_signature.signature_config'))
            ->addArgument($config['replay_protection']['enabled'])
            ->addArgument($config['algorithm'])
            ->addArgument($config['secret'])
        ;

        return $signatureConfigId;
    }

    public function createSignatureQueryParameters(ContainerBuilder $container, string $firewallName, array $config): string
    {
        $signatureQueryParametersId = 'rezzza.security.request_signature.signature_query_parameters.'.$firewallName;
        $container
            ->setDefinition($signatureQueryParametersId, $this->createDefinition('rezzza.security.request_signature.signature_query_parameters'))
            ->addArgument($config['parameter'])
            ->addArgument($config['replay_protection']['parameter'])
        ;

        return $signatureQueryParametersId;
    }

    public function createReplayProtection(ContainerBuilder $container, string $firewallName, array $config): string
    {
        $replayProtectionId = 'rezzza.security.request_signature.replay_protection.'.$firewallName;
        $container
            ->setDefinition($replayProtectionId, $this->createDefinition('rezzza.security.request_signature.replay_protection'))
            ->addArgument($config['replay_protection']['enabled'])
            ->addArgument($config['replay_protection']['lifetime'])
        ;

        return $replayProtectionId;
    }

    public function addConfiguration(NodeDefinition $node): void
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

    private function createDefinition(string $serviceId): ChildDefinition
    {
        return new ChildDefinition($serviceId);
    }
}
