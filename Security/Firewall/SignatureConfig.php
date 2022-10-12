<?php

namespace Rezzza\SecurityBundle\Security\Firewall;

use GuzzleHttp\Psr7\Request;

class SignatureConfig
{
    private $replayProtectionEnabled;

    private $algorithm;

    private $secret;

    /**
     * @var int
     */
    private $ttl;

    public function __construct($replayProtectionEnabled, $algorithm, $secret, $ttl = 0)
    {
        $this->replayProtectionEnabled = (bool) $replayProtectionEnabled;
        $this->algorithm = $algorithm;
        $this->secret = $secret;
        $this->ttl = $ttl;
    }

    public function isReplayProtectionEnabled()
    {
        return true === $this->replayProtectionEnabled;
    }

    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return int
     */
    public function getTtl()
    {
        return $this->ttl;
    }
}
