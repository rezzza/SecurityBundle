<?php

namespace Rezzza\SecurityBundle\Security\Firewall;

use GuzzleHttp\Psr7\Request;

class SignatureConfig
{
    private $replayProtectionEnabled;

    private $algorithm;

    private $secret;

    public function __construct($replayProtectionEnabled, $algorithm, $secret)
    {
        $this->replayProtectionEnabled = (bool) $replayProtectionEnabled;
        $this->algorithm = $algorithm;
        $this->secret = $secret;
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
}
