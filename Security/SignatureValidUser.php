<?php

declare(strict_types=1);

namespace Rezzza\SecurityBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class SignatureValidUser implements UserInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): ?string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getUserIdentifier(): string
    {
        return '';
    }
}
