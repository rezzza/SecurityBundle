<?php

declare(strict_types=1);

namespace Rezzza\SecurityBundle\Security\Firewall;

class SignatureConfig
{
    public function __construct(
        private bool $replayProtectionEnabled,
        private string $algorithm,
        private string $secret,
        private int $ttl = 0,
    ) {
    }

    public function isReplayProtectionEnabled(): bool
    {
        return true === $this->replayProtectionEnabled;
    }

    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getTtl(): int
    {
        return $this->ttl;
    }
}
