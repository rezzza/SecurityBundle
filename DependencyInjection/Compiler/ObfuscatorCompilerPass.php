<?php

declare(strict_types=1);

namespace Rezzza\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ObfuscatorCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
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
