<?php

namespace Rezzza\SecurityBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Rezzza\SecurityBundle\Security\RequestSignatureToken;
use Rezzza\SecurityBundle\Security\RequestSignature\RequestSignatureBuilder;
use Rezzza\SecurityBundle\Security\Firewall\RequestSignatureEntryPoint;
use Rezzza\SecurityBundle\Security\Firewall\Context;

/**
 * RequestSignatureProvider
 *
 * @uses AuthenticationProviderInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RequestSignatureProvider implements AuthenticationProviderInterface
{
    /**
     * @var RequestSignatureBuilder
     */
    private $builder;

    /**
     * @var RequestSignatureEntryPoint
     */
    private $entryPoint;

    /**
     * @param RequestSignatureBuilder    $builder    builder
     * @param RequestSignatureEntryPoint $entryPoint entryPoint
     */
    public function __construct(RequestSignatureBuilder $builder, RequestSignatureEntryPoint $entryPoint)
    {
        $this->builder    = $builder;
        $this->entryPoint = $entryPoint;
    }

    /**
     * @param TokenInterface $token token
     *
     * @throws AuthenticationException
     * @return TokenInterface
     */
    public function authenticate(TokenInterface $token)
    {
        $context = new Context();
        $context->hydrateWithToken($token);
        $context->hydrateWithEntryPoint($this->entryPoint);

        $signature = $this->builder->build($context);

        if ($signature != $token->signature) {
            throw new AuthenticationException('Invalid signature');
        }

        if ($this->entryPoint->get('replay_protection')) {
            $date = $token->signatureTime;

            if (!is_numeric($date)) {
                throw new NonceExpiredException('Signature ttl is not valid');
            }

            if ($this->entryPoint->get('replay_protection_lifetime') < abs(time() - $date)) {
                throw new NonceExpiredException('Signature has expired');
            }
        }

        return $token;
    }

    /**
     * @param TokenInterface $token token
     *
     * @return boolean
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof RequestSignatureToken;
    }
}
