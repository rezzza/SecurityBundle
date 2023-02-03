<?php

declare(strict_types=1);

namespace Rezzza\SecurityBundle\Request\Obfuscator;

/**
 * ObfuscatorInterface.
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
interface ObfuscatorInterface
{
    /**
     * @param array $data               data
     * @param array $obfuscatedPatterns obfuscatedPatterns
     *
     * @return array
     */
    public function obfuscate(array $data, array $obfuscatedPatterns);
}
