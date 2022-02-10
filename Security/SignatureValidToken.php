<?php

namespace Rezzza\SecurityBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * RequestSignatureToken
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class SignatureValidToken extends AbstractToken
{
    public $signature;

    public $signatureTime;

    public function __construct($signature, $signatureTime, array $roles = array())
    {
        parent::__construct($roles);
        $this->signature = $signature;
        $this->signatureTime = $signatureTime;
        $this->setAuthenticated(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return '';
    }
}
