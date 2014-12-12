<?php
namespace Hangman\Bundle\ApiBundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HangmanApiBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
    }
}
