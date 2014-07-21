<?php

namespace Rezzza\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class ObfuscatorCompilerPass implements CompilerPassInterface
{
     /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // request obfuscator is not enabled.
        if (!$container->getParameter('rezzza.security.request_obfuscator.enabled')) {
            return;
        }

        $container->setParameter('data_collector.request.class', 'Rezzza\SecurityBundle\DataCollector\RequestDataCollector');

        $container->getDefinition('data_collector.request')
            ->addArgument(new Reference('annotation_reader'))
            ->addArgument(new Reference('rezzza.security.request_obfuscator.obfuscator'));
    }
}
