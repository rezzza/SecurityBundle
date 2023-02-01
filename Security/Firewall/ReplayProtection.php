<?php

namespace Rezzza\SecurityBundle\Security\Firewall;

class ReplayProtection
{
    public function __construct(private bool $enabled, private int $lifetime)
    {
    }

    /**
     * @param integer $signatureTime Should be a unix timestamp
     * @param integer $referenceTime Should be a unix timestamp
     */
    public function accept(?int $signatureTime, int $referenceTime): bool
    {
        if (!$this->enabled) {
            return true;
        }

        // We validate only now the signatureTime because before we are not sure we need it.
        if (null === $signatureTime) {
            throw new ExpiredSignatureException(sprintf('Signature TTL "%s" is not valid', $signatureTime));
        }

        return $this->lifetime >= abs($referenceTime - $signatureTime);
    }
}
