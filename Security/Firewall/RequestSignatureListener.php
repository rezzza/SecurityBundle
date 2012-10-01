<?php

namespace Rezzza\SecurityBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Rezzza\SecurityBundle\Security\RequestSignatureToken;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * RequestSignatureListener
 *
 * @uses ListenerInterface
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RequestSignatureListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;
    protected $entryPoint;

    /**
     * @param SecurityContextInterface       $securityContext       securityContext
     * @param AuthenticationManagerInterface $authenticationManager authenticationManager
     * @param LoggerInterface                $logger                logger
     * @param RequestSignatureEntryPoint     $entryPoint            entryPoint
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, LoggerInterface $logger, RequestSignatureEntryPoint $entryPoint)
    {
        $this->securityContext       = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->logger                = $logger;
        $this->entryPoint            = $entryPoint;
    }

    public function handle(GetResponseEvent $event)
    {
        if ($this->entryPoint->isIgnored()) {
            if (null !== $this->securityContext->getToken()) {
                return;
            }

            $this->securityContext->setToken(new AnonymousToken('request_signature', 'anon.', array()));
            return;
        }

        $request   = $event->getRequest();
        $parameter = $this->entryPoint->get('parameter');
        if (null !== $signature = $request->get($parameter)) {

            $token = new RequestSignatureToken();
            $token->request   = $request;
            $token->signature = $signature;

            if ($this->entryPoint->get('replay_protection')) {
                $token->signatureTime = $request->get($this->entryPoint->get('replay_protection_parameter'));
            }

            try {
                $returnValue = $this->authenticationManager->authenticate($token);

                if ($returnValue instanceof TokenInterface) {
                    return $this->securityContext->setToken($returnValue);
                } elseif ($returnValue instanceof Response) {
                    return $event->setResponse($returnValue);
                }
            } catch (AuthenticationException $e) {
                $this->logger->info(sprintf('Authentication request failed: %s', $e->getMessage()));
            }
        }

        $response = new Response();
        $response->setStatusCode(403);
        $event->setResponse($response);
    }
}
