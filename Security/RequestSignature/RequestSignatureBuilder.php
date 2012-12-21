<?php

namespace Rezzza\SecurityBundle\Security\RequestSignature;

use Rezzza\SecurityBundle\Security\Firewall\RequestSignatureEntryPoint;
use Rezzza\SecurityBundle\Security\Authentication\RequestDataCollector;

/**
 * RequestSignatureBuilder
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RequestSignatureBuilder
{
    /**
     * @param RequestDataCollector       $collector  collector
     * @param RequestSignatureEntryPoint $entryPoint entryPoint
     */
    public function build(RequestDataCollector $collector, RequestSignatureEntryPoint $entryPoint)
    {
        $payload   = array();

        if ($entryPoint->get('replay_protection')) {
            $payload[] = $collector->signatureTime;
        }

        $payload[] = $collector->requestMethod;
        $payload[] = $collector->httpHost;
        $payload[] = $collector->pathInfo;
        $payload[] = $collector->content;

        $payload   = implode("\n", $payload);

        $signature = hash_hmac($entryPoint->get('algorithm'), $payload, $entryPoint->get('secret'));

        return $signature;
    }
}
