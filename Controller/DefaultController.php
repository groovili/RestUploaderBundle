<?php

namespace Groovili\RestUploaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @package Groovili\RestUploaderBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(): Response
    {
       return new Response('Hello World!');
    }
}
