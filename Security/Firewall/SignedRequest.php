<?php

namespace Rezzza\SecurityBundle\Security\Firewall;

class SignedRequest
{
    private $method;

    private $host;

    private $pathInfo;

    private $content;

    private $signatureTime;

    public function __construct($method, $host, $pathInfo, $content, $signatureTime)
    {
        $this->method = strtoupper($method);
        $this->host = $host;
        $this->pathInfo = $pathInfo;
        $this->content = $content;
        $this->signatureTime = $signatureTime;
    }

    public function buildSignature(SignatureConfig $signatureConfig)
    {
        $payload = array(
            $this->method,
            $this->host,
            $this->pathInfo,
            $this->content
        );

        if ($signatureConfig->isReplayProtectionEnabled()) {
            // use unshift to keep BC on signature generation
            array_unshift($payload, $this->signatureTime);
        }

        return hash_hmac(
            $signatureConfig->getAlgorithm(),
            implode("\n", $payload),
            $signatureConfig->getSecret()
        );
    }

    public function authenticateSignature($signature, SignatureConfig $signatureConfig, ReplayProtection $replayProtection)
    {
        if ($signature !== $this->buildSignature($signatureConfig)) {
            throw new InvalidSignatureException;
        }

        if (!$replayProtection->accept($this->signatureTime, time())) {
            throw new ExpiredSignatureException('Signature has expired');
        }

        return true;
    }
}
