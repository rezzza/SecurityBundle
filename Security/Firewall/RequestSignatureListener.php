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

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, RequestSignatureEntryPoint $entryPoint)
    {
        $this->securityContext       = $securityContext;
        $this->authenticationManager = $authenticationManager;
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
        $parameter = $request->get($this->entryPoint->get('parameter'));
        if (null !== $request->get($parameter)) {

            $token = new RequestSignatureToken();
            $token->request   = $request;
            $token->signature = $request->get($parameter);

            if ($this->entryPoint->get('replay_protection')) {
                $token->signatureLifetime = $request->get($this->entryPoint->get('replay_protection_parameter'));
            }

            try {
                $returnValue = $this->authenticationManager->authenticate($token);

                if ($returnValue instanceof TokenInterface) {
                    return $this->securityContext->setToken($returnValue);
                } elseif ($returnValue instanceof Response) {
                    return $event->setResponse($returnValue);
                }
            } catch (AuthenticationException $e) {
                // you might log something here
            }
        }

        $response = new Response();
        $response->setStatusCode(403);
        $event->setResponse($response);
    }
}
