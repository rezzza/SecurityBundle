<?php

namespace Rezzza\SecurityBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\Definition\Processor;
use Rezzza\SecurityBundle\Security\Firewall\Context;
use Symfony\Component\DependencyInjection\Definition;

/**
 * RezzzaSecurityExtension
 *
 * @uses Extension
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RezzzaSecurityExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));

        $loader->load('security.xml');

        $container->setParameter('rezzza.security.request_obfuscator.enabled', $config['request_obfuscator']['enabled']);

        if ($container->getParameter('rezzza.security.request_obfuscator.enabled')) {
            $loader->load('request_obfuscator.xml');
        }

        $firewalls = $config['firewalls'];

        foreach ($firewalls as $name => $data) {
            $serviceName = sprintf('rezzza.security.signature_config.%s', $name);

            $definition = new Definition('Rezzza\SecurityBundle\Security\Firewall\SignatureConfig');
            $definition->addArgument($data['replay_protection']);
            $definition->addArgument($data['algorithm']);
            $definition->addArgument($data['secret']);
            $definition->setPublic(true);

            $container->setDefinition($serviceName, $definition);
        }
    }
}
