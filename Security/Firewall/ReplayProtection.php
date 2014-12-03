<?php

namespace Rezzza\SecurityBundle\Security\Firewall;

class ReplayProtection
{
    private $enabled;

    private $lifetime;

    public function __construct($enabled, $lifetime)
    {
        $this->guardValidLifetime($lifetime);
        $this->enabled = (bool) $enabled;
        $this->lifetime = (int) $lifetime;
    }

    /**
     * @param integer $signatureTime Should be a unix timestamp
     * @param integer $referenceTime Should be a unix timestamp
     */
    public function accept($signatureTime, $referenceTime)
    {
        if (!$this->enabled) {
            return true;
        }

        // We validate only now the signatureTime because before we are not sure we need it.
        if (!is_numeric($signatureTime)) {
            throw new ExpiredSignatureException(sprintf('Signature TTL "%s" is not valid', $signatureTime));
        }

        return $this->lifetime >= abs($referenceTime - $signatureTime);
    }

    private function guardValidLifetime($lifetime)
    {
        if (!is_numeric($lifetime)) {
            throw new \LogicException('ReplayProtection lifetime should be a numeric value');
        }
    }
}
