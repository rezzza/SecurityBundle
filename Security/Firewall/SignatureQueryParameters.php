<?php

declare(strict_types=1);

namespace Rezzza\SecurityBundle\Security\Firewall;

class SignatureQueryParameters
{
    public function __construct(private string $nonceQueryParameter, private string $timeQueryParameter)
    {
    }

    public function getNonceQueryParameter(): string
    {
        return $this->nonceQueryParameter;
    }

    public function getTimeQueryParameter(): string
    {
        return $this->timeQueryParameter;
    }
}
