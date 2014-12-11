<?php

namespace Rezzza\SecurityBundle\Security\Firewall;

class SignatureQueryParameters
{
    private $nonceQueryParameter;

    private $timeQueryParameter;

    public function __construct($nonceQueryParameter, $timeQueryParameter)
    {
        $this->nonceQueryParameter = $nonceQueryParameter;
        $this->timeQueryParameter = $timeQueryParameter;
    }

    public function getNonceQueryParameter()
    {
        return $this->nonceQueryParameter;
    }

    public function getTimeQueryParameter()
    {
        return $this->timeQueryParameter;
    }
}
