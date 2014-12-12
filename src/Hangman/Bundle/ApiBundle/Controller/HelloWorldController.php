<?php
namespace Hangman\Bundle\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HelloWorldController extends Controller
{
    public function helloWorldAction()
    {
        return new Response(
            'Hello world!'
        );
    }
} 