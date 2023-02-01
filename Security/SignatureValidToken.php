<?php

namespace Rezzza\SecurityBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class SignatureValidToken extends AbstractToken
{
    public function __construct(SignatureValidUser $user)
    {
        $this->setUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(): string
    {
        return '';
    }
}
