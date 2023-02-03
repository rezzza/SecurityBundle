<?php

declare(strict_types=1);

namespace Rezzza\SecurityBundle\Controller\Annotations;

/**
 * @Annotation()
 *
 * ObfuscateRequest
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class ObfuscateRequest
{
    /**
     * @var array<string>
     */
    private $obfuscatedPatterns;

    /**
     * @param array $data data
     */
    public function __construct(array $obfuscatedPatterns)
    {
        $this->obfuscatedPatterns = $obfuscatedPatterns;
    }

    /**
     * @return array<string>
     */
    public function getObfuscatedPatterns()
    {
        return $this->obfuscatedPatterns;
    }
}
