<?php

namespace Rezzza\SecurityBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Rezzza\SecurityBundle\Security\RequestSignatureToken;
use Rezzza\SecurityBundle\Security\SignatureValidToken;
use Rezzza\SecurityBundle\Security\Firewall\SignedRequest;
use Rezzza\SecurityBundle\Security\Firewall\SignatureConfig;
use Rezzza\SecurityBundle\Security\Firewall\ReplayProtection;
use Rezzza\SecurityBundle\Security\Firewall\InvalidSignatureException;
use Rezzza\SecurityBundle\Security\Firewall\ExpiredSignatureException;

class RequestSignatureProvider implements AuthenticationProviderInterface
{
    private $signatureConfig;

    private $replayProtection;

    public function __construct(SignatureConfig $signatureConfig, ReplayProtection $replayProtection)
    {
        $this->signatureConfig = $signatureConfig;
        $this->replayProtection = $replayProtection;
    }

    public function authenticate(TokenInterface $token)
    {
        try {
            $signedRequest = new SignedRequest(
                $token->requestMethod,
                $token->requestHost,
                $token->requestPathInfo,
                $token->requestContent,
                $token->signatureTime
            );

            $signedRequest->authenticateSignature($token->signature, $this->signatureConfig, $this->replayProtection);

            return new SignatureValidToken($token->signature, $token->signatureTime);
        } catch (InvalidSignatureException $e) {
            throw new AuthenticationException('Invalid signature', null, $e);
        } catch (ExpiredSignatureException $e) {
            throw new NonceExpiredException($e->getMessage(), null, $e);
        }
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
