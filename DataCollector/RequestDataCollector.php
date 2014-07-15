<?php

namespace Rezzza\SecurityBundle\DataCollector;

use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\RequestDataCollector as BaseRequestDataCollector;
use Rezzza\SecurityBundle\Controller\Annotations\ObfuscateRequest;
use Rezzza\SecurityBundle\Request\Obfuscator\ObfuscatorInterface;

class RequestDataCollector extends BaseRequestDataCollector
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var Obfuscator
     */
    private $obfuscator;

    /**
     * @param AnnotationReader    $annotationReader annotationReader
     * @param ObfuscatorInterface $obfuscator       obfuscator
     */
    public function __construct(AnnotationReader $annotationReader, ObfuscatorInterface $obfuscator)
    {
        $this->annotationReader = $annotationReader;
        $this->obfuscator       = $obfuscator;

        parent::__construct();
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        parent::collect($request, $response, $exception);

        $controller = explode('::', $request->get('_controller'));

        if (count($controller) !== 2) {
            return;
        }

        $class            = new \ReflectionClass($controller[0]);
        $reflectionMethod = $class->getMethod($controller[1]);
        $annotations      = $this->annotationReader->getMethodAnnotations($reflectionMethod);

        foreach ($annotations as $annotation) {
            if ($annotation instanceof ObfuscateRequest) {
                $this->data = $this->obfuscator->obfuscate($this->data, $annotation->getObfuscatedPatterns());
            }
        }
    }
}
