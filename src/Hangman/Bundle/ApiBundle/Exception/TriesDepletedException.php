<?php

namespace Hangman\Bundle\ApiBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class TriesDepletedException extends HttpException
{
}