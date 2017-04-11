<?php

namespace Rezzza\SecurityBundle\Security\Firewall;

class SignedRequest
{
    private $method;

    private $host;

    private $pathInfo;

    private $content;

    private $signatureTime;

    public function __construct($method, $host, $pathInfo, $content, $signatureTime = null)
    {
        $this->setMethod(strtoupper($method));
        $this->signatureTime = $signatureTime;
        $this->host = $host;
        $this->pathInfo = $pathInfo;
        $this->content = $content;
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
            $this->guardValidSignatureTime();
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

    private function guardValidSignatureTime()
    {
        if (!is_numeric($this->signatureTime)) {
            throw new InvalidSignatureException(
                sprintf('Signed request accepts only numeric value for "signatureTime" attribute. "%s" given', $this->signatureTime)
            );
        }
    }

    private function setMethod($method)
    {
        $httpVerbs = array('POST', 'GET', 'PUT', 'PATCH', 'LINK');

        if (!in_array($method, $httpVerbs)) {
            throw new InvalidSignatureException(
                sprintf('Signed request accepts only valid http method for "method" attribute. "%s" given', $method)
            );
        }

        $this->method = $method;
    }
}
