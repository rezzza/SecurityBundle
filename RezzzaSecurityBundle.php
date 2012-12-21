<?php

namespace Rezzza\SecurityBundle;

use Rezzza\SecurityBundle\DependencyInjection\Security\Factory\RequestSignatureFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * RezzzaSecurityBundle
 *
 * @uses Bundle
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RezzzaSecurityBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');

        // 2.0 does not support this.
        if (method_exists('\Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension', 'addSecurityListenerFactory')) {
            $extension->addSecurityListenerFactory(new RequestSignatureFactory());
        }
    }
}
