<?php

namespace Rezzza\SecurityBundle\Security\RequestSignature;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Rezzza\SecurityBundle\Security\Firewall\RequestSignatureEntryPoint;

/**
 * RequestSignatureBuilder
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RequestSignatureBuilder
{
    private $signature;
    private $ttl;

    /**
     * @param TokenInterface             $token      token
     * @param RequestSignatureEntryPoint $entryPoint entryPoint
     */
    public function build(TokenInterface $token, RequestSignatureEntryPoint $entryPoint)
    {
        $request = $token->request;

        $payload   = array();

        if ($entryPoint->get('replay_protection')) {
            $payload[] = $token->signatureLifetime;
        }

        $payload[] = $request->server->get('REQUEST_METHOD');
        $payload[] = $request->server->get('HTTP_HOST');
        $payload[] = $request->getPathInfo();
        $payload[] = $request->getContent();

        $payload   = implode('-', array_filter($payload));

        $this->signature = hash_hmac($entryPoint->get('algorythm'), $payload, $entryPoint->get('secret'));
        $this->ttl       = $entryPoint->get('replay_protection_lifetime');
    }

    /**
     * @param TokenInterface $token token
     *
     * @return boolean
     */
    public function signatureEquals(TokenInterface $token)
    {
        return $token->signature === $this->signature;
    }

    /**
     * @param TokenInterface $token token
     *
     * @return boolean
     */
    public function hasExpired(TokenInterface $token)
    {
        $date = $token->signatureLifetime;

        if (!is_numeric($date)) {
            return true;
        }

        return $this->ttl < abs(time() - $date);
    }
}
