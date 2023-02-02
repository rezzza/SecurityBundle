<?php

declare(strict_types=1);

namespace Rezzza\SecurityBundle;

use Rezzza\SecurityBundle\DependencyInjection\Compiler;
use Rezzza\SecurityBundle\DependencyInjection\Security\Factory\RequestSignatureFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * RezzzaSecurityBundle.
 *
 * @uses Bundle
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RezzzaSecurityBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addAuthenticatorFactory(new RequestSignatureFactory());

        $container->addCompilerPass(new Compiler\ObfuscatorCompilerPass());
    }
}
