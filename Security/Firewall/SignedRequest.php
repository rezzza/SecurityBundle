<?php

declare(strict_types=1);

namespace Rezzza\SecurityBundle\Security\Firewall;

class SignedRequest
{
    public function __construct(
        private string $method,
        private string $host,
        private string $pathInfo,
        private string $content,
        private ?int $signatureTime = null,
    ) {
        $this->setMethod(strtoupper($method));
    }

    public function buildSignature(SignatureConfig $signatureConfig): string
    {
        $payload = [
            $this->method,
            $this->host,
            $this->pathInfo,
            $this->content,
        ];

        if ($signatureConfig->isReplayProtectionEnabled()) {
            $this->guardValidSignatureTime();
            // use unshift to keep BC on signature generation
            array_unshift($payload, $this->signatureTime);
        }

        return hash_hmac(
            $signatureConfig->getAlgorithm(),
            implode("\n", $payload),
            $signatureConfig->getSecret(),
        );
    }

    public function authenticateSignature(string $signature, SignatureConfig $signatureConfig, ReplayProtection $replayProtection): bool
    {
        if ($signature !== $this->buildSignature($signatureConfig)) {
            throw new InvalidSignatureException();
        }

        if (!$replayProtection->accept($this->signatureTime, time())) {
            throw new ExpiredSignatureException('Signature has expired');
        }

        return true;
    }

    private function guardValidSignatureTime(): void
    {
        if (null === $this->signatureTime) {
            throw new InvalidSignatureException(sprintf('Signed request accepts only numeric value for "signatureTime" attribute. "%s" given', $this->signatureTime));
        }
    }

    private function setMethod(string $method): void
    {
        $httpVerbs = ['POST', 'GET', 'PUT', 'PATCH', 'LINK', 'DELETE'];

        if (!\in_array($method, $httpVerbs, true)) {
            throw new InvalidSignatureException(sprintf('Signed request accepts only valid http method for "method" attribute. "%s" given', $method));
        }

        $this->method = $method;
    }
}
