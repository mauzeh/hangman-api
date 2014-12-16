<?php

namespace Hangman\Bundle\ApiBundle\Tests;

use FOS\RestBundle\View\View;
use Hangman\Bundle\ApiBundle\GameProcessor;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;

/**
 * Class GameProcessorTest
 *
 * @package Hangman\Bundle\ApiBundle\Tests
 */
class GameProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests a successful game.
     */
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

    /**
     * Tests a failing game.
     */
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

    /**
     * Tests a busy game.
     */
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

    /**
     * Tests supplying an invalid character.
     */
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
        $this->assertEquals(GameProcessor::ERROR_INVALID_CHARACTER, $response->getData()['errors'][0]['code']);
        $this->assertCount(1, $response->getData()['errors']);
    }

    /**
     * Tests supplying an invalid character.
     */
    public function testGameNotFound()
    {
        $view = View::create();
        $character = '-';

        $em = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $em->expects($this->never())
            ->method('flush');

        $processor = new GameProcessor($em);
        $response = $processor->process($view, null, $character);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(GameProcessor::ERROR_GAME_NOT_FOUND, $response->getData()['errors'][0]['code']);
        $this->assertCount(1, $response->getData()['errors']);
    }

    /**
     * Tests a game that has no more tries left.
     */
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
        $this->assertEquals(GameProcessor::ERROR_TRIES_DEPLETED, $response->getData()['errors'][0]['code']);
        $this->assertCount(1, $response->getData()['errors']);
        $this->assertEquals(0, $game->getTriesLeft());
    }

    /**
     * Tests supplying a character that was already tried before.
     */
    public function testGameRetryCharacter()
    {
        $view = View::create();
        $game = new Game();
        $game->setWord('something');
        $game->setCharactersGuessed(array('a'));
        $character = 'a';

        $em = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $em->expects($this->never())
            ->method('flush');

        $processor = new GameProcessor($em);
        $response = $processor->process($view, $game, $character);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(GameProcessor::ERROR_CHARACTER_NOT_NEW, $response->getData()['errors'][0]['code']);
        $this->assertCount(1, $response->getData()['errors']);
        $this->assertEquals(11, $game->getTriesLeft());
    }
}
