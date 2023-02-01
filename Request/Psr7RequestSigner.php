<?php

namespace Rezzza\SecurityBundle\Request;

use Rezzza\SecurityBundle\Security\Firewall\SignatureConfig;
use Rezzza\SecurityBundle\Security\Firewall\SignedRequest;
use Psr\Http\Message\RequestInterface;

/**
 * Psr7RequestSigner
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Psr7RequestSigner
{
    public function __construct(private SignatureConfig $signatureConfig)
    {
    }

    public function sign(RequestInterface $request): RequestInterface
    {
        $replayProtectionEnabled = $this->signatureConfig->isReplayProtectionEnabled();
        $time = $replayProtectionEnabled ? time() + $this->signatureConfig->getTtl() : null;

        $signedRequest = new SignedRequest(
            $request->getMethod(),
            $request->getUri()->getHost(),
            $request->getUri()->getPath(),
            (string) $request->getBody(),
            $time
        );

        $signature = $signedRequest->buildSignature($this->signatureConfig);
        $uri = $request->getUri();

        $query = $uri->getQuery();
        if (false === empty($query)) {
            $query .= '&';
        }
        $query .= '_signature='.$signature;

        if ($replayProtectionEnabled) {
            $query .= '&_signature_ttl='.$time;
        }

        $uri = $uri->withQuery($query);

        return $request->withUri($uri);
    }
}
