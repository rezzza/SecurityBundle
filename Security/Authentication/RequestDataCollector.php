<?php

namespace Rezzza\SecurityBundle\Security\Authentication;

use Rezzza\SecurityBundle\Security\RequestSignatureToken;

/**
 * RequestDataCollector
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RequestDataCollector
{
    /**
     * @var string
     */
    public $requestMethod;

    /**
     * @var string
     */
    public $httpHost;

    /**
     * @var string
     */
    public $pathInfo;

    /**
     * @var string
     */
    public $content;

    /**
     * @var integer
     */
    public $signatureTime;

    /**
     * @param RequestSignatureToken $token token
     *
     * @return RequestDataCollector
     */
    public static function buildFromToken(RequestSignatureToken $token)
    {
        $request = $token->request;

        $instance = new static();
        $instance->requestMethod = $request->server->get('REQUEST_METHOD');
        $instance->httpHost      = $request->server->get('HTTP_HOST');
        $instance->pathInfo      = $request->getPathInfo();
        $instance->content       = $request->getContent();
        $instance->signatureTime = $token->signatureTime;

        return $instance;
    }

    /**
     * @param string $method   method
     * @param string $host     host, format = subdomain.domain.tld
     * @param string $pathInfo pathInfo, format = /api/......
     * @param string $content  content, RAW_DATA
     * @param string $time     time, timestamp of request time.
     *
     * @return RequestDataCollector
     */
    public static function create($method = null, $host = null, $pathInfo = null, $content = null, $time = null)
    {
        $instance = new static();
        $instance->requestMethod = strtoupper($method);
        $instance->httpHost      = $host;
        $instance->pathInfo      = $pathInfo;
        $instance->content       = $content;
        $instance->signatureTime = null === $time ? time() : $time;

        return $instance;
    }
}
