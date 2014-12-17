<?php

namespace Hangman\Bundle\ApiBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class TriesDepletedException
 *
 * @package Hangman\Bundle\ApiBundle\Exception
 */
class TriesDepletedException extends HttpException
{
}