<?php

declare(strict_types=1);

namespace Rezzza\SecurityBundle\DataCollector;

use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Rezzza\SecurityBundle\Request\Obfuscator\ObfuscatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\RequestDataCollector as BaseRequestDataCollector;

class RequestDataCollector extends BaseRequestDataCollector
{
    public function __construct(private AnnotationReader $annotationReader, private ObfuscatorInterface $obfuscator)
    {
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        parent::collect($request, $response, $exception);

        $controller = explode('::', $request->get('_controller'));

        if (2 !== \count($controller)) {
            return;
        }

        $class = new \ReflectionClass($controller[0]);
        $reflectionMethod = $class->getMethod($controller[1]);
        $annotation = $this->annotationReader->getMethodAnnotation($reflectionMethod, '\Rezzza\SecurityBundle\Controller\Annotations\ObfuscateRequest');

        if ($annotation) {
            $this->data = $this->obfuscator->obfuscate($this->data, $annotation->getObfuscatedPatterns());
        }
    }
}
