<?php

namespace Rezzza\SecurityBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Rezzza\SecurityBundle\Security\RequestSignatureToken;
use Psr\Log\LoggerInterface;

trait RequestSignatureListenerTrait
{
    protected $tokenStorage;
    protected $authenticationManager;
    protected $signatureQueryParameters;
    protected $ignored;
    protected $logger;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager,
        SignatureQueryParameters $signatureQueryParameters,
        $ignored,
        LoggerInterface $logger = null
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->signatureQueryParameters = $signatureQueryParameters;
        $this->ignored = $ignored;
        $this->logger = $logger;
    }

    public function handleOrInvoke(/* RequestEvent */$event)
    {
        if (true === $this->ignored) {
            if (null !== $this->tokenStorage->getToken()) {
                return;
            }

            $this->tokenStorage->setToken(new AnonymousToken('request_signature', 'anon.', array()));

            return;
        }

        $request = $event->getRequest();
        $authToken = new RequestSignatureToken;
        $authToken->signature = $request->get($this->signatureQueryParameters->getNonceQueryParameter());
        $authToken->signatureTime = $request->get($this->signatureQueryParameters->getTimeQueryParameter());
        $authToken->requestMethod = $request->server->get('REQUEST_METHOD');
        $authToken->requestHost = $request->server->get('HTTP_HOST');
        $authToken->requestPathInfo = $request->getPathInfo();
        $authToken->requestContent = rawurldecode($request->getContent());

        try {
            return $this->tokenStorage->setToken(
                $this->authenticationManager->authenticate($authToken)
            );
        } catch (AuthenticationException $e) {
            if ($this->logger) {
                $this->logger->info(sprintf('Authentication request failed: %s', $e->getMessage()));
            }
        }

        $response = new Response();
        $response->setStatusCode(403);
        $event->setResponse($response);
    }
}