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
    /** @var SignatureConfig */
    private $signatureConfig;

    /**
     * @param SignatureConfig $signatureConfig signatureConfig
     */
    public function __construct(SignatureConfig $signatureConfig)
    {
        $this->signatureConfig = $signatureConfig;
    }

    /**
     * @param RequestInterface $request request
     *
     * @return Request
     */
    public function sign(RequestInterface $request)
    {
        $replayProtectionEnabled = $this->signatureConfig->isReplayProtectionEnabled();
        $time = $replayProtectionEnabled ? time() : null;

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
        $query .= (empty($query)) ? '?' : '&';
        $query .= '_signature='.$signature;

        if ($replayProtectionEnabled) {
            $query .= '&_signature_ttl='.$time;
        }

        $uri = $uri->withQuery($query);

        return $request->withUri($uri);
    }
}
