<?php

declare(strict_types=1);

namespace Rezzza\SecurityBundle\Security\Firewall;

use Rezzza\SecurityBundle\Security\SignatureValidToken;
use Rezzza\SecurityBundle\Security\SignatureValidUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class RequestSignatureListener extends AbstractAuthenticator
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private SignatureQueryParameters $signatureQueryParameters,
        private bool $ignored,
        private SignatureConfig $signatureConfig,
        private ReplayProtection $replayProtection,
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        if (true === $this->ignored) {
            return new SelfValidatingPassport(
                new UserBadge('anon.', static fn () => new SignatureValidUser()),
            );
        }

        $signature = $request->get($this->signatureQueryParameters->getNonceQueryParameter());

        if (null === $signature) {
            throw new UnauthorizedHttpException('Signature must be filled.');
        }

        $signedRequest = new SignedRequest(
            $request->server->get('REQUEST_METHOD'),
            $request->server->get('HTTP_HOST'),
            $request->getPathInfo(),
            rawurldecode($request->getContent()),
            $request->get($this->signatureQueryParameters->getTimeQueryParameter()),
        );

        try {
            $signedRequest->authenticateSignature($signature, $this->signatureConfig, $this->replayProtection);
        } catch (InvalidSignatureException $e) {
            throw new UnauthorizedHttpException('Invalid signature');
        } catch (ExpiredSignatureException $e) {
            throw new HttpException(408, $e->getMessage());
        }

        return new SelfValidatingPassport(
            new UserBadge($signature, static fn () => new SignatureValidUser()),
        );
    }

    public function supports(Request $request): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response(null, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        $badge = $passport->getBadge(UserBadge::class);
        if (!$badge instanceof UserBadge) {
            throw new \LogicException('No UserBadge configured for this passport.');
        }

        $user = $badge->getUser();
        if (!$badge->getUser() instanceof SignatureValidUser) {
            throw new \LogicException('No SignatureValidUser configured for this UserBadge.');
        }

        $token = new SignatureValidToken($user);

        // BC layer for Authorization::isGranted() in sf5
        if (method_exists($token, 'setAuthenticated')) {
            $token->setAuthenticated(true);
        }

        return $token;
    }
}
