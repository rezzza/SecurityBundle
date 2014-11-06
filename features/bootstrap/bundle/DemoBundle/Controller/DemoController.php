<?php

namespace Rezzza\Bundle\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DemoController extends Controller
{
    public function helloAction(Request $request)
    {
        return new Response('posay');
    }
}

