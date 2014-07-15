<?php

namespace Rezzza\SecurityBundle\Request\Obfuscator;

use Symfony\Component\HttpFoundation\Request;

/**
 * ObfuscatorInterface
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
