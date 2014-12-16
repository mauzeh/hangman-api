<?php

namespace Hangman\Bundle\ApiBundle\Tests;

use FOS\RestBundle\View\View;
use Hangman\Bundle\ApiBundle\GameProcessor;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;

class GameProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testGameSuccess()
    {
        $view = View::create();
        $game = new Game();
        $game->setWord('fool');
        $game->setCharactersGuessed(array(
            'f', 'l', 'h', 'i', 'n', 'g', 'q', 'z',
        ));
        $game->setTriesLeft(6);
        $character = 'o';

        $em = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $em->expects($this->once())
            ->method('flush');

        $processor = new GameProcessor($em);
        $response = $processor->process($view, $game, $character);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(Game::STATUS_SUCCESS, $game->getStatus());
        $this->assertEquals(6, $game->getTriesLeft());
    }

    public function testGameFail()
    {
        $view = View::create();
        $game = new Game();
        $game->setWord('a');
        $game->setTriesLeft(1);
        $character = 'b';

        $em = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $em->expects($this->once())
            ->method('flush');

        $processor = new GameProcessor($em);
        $response = $processor->process($view, $game, $character);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(Game::STATUS_FAIL, $game->getStatus());
        $this->assertEquals(0, $game->getTriesLeft());
    }

    public function testGameBusy()
    {
        $view = View::create();
        $game = new Game();
        $game->setWord('something');
        $game->setTriesLeft(11);
        $character = 'z';

        $em = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $em->expects($this->once())
            ->method('flush');

        $processor = new GameProcessor($em);
        $response = $processor->process($view, $game, $character);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(10, $game->getTriesLeft());
        $this->assertEquals(array('z'), $game->getCharactersGuessed());
    }

    public function testInvalidCharacter()
    {
        $view = View::create();
        $game = new Game();
        $character = '-';

        $em = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $em->expects($this->never())
            ->method('flush');

        $processor = new GameProcessor($em);
        $response = $processor->process($view, $game, $character);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testGameNoMoreTries()
    {
        $view = View::create();
        $game = new Game();
        $game->setWord('something');
        $game->setTriesLeft(0);
        $character = 'a';

        $em = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $em->expects($this->never())
            ->method('flush');

        $processor = new GameProcessor($em);
        $response = $processor->process($view, $game, $character);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(0, $game->getTriesLeft());
    }
}
