<?php

namespace Hangman\Bundle\ApiBundle\Tests;

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
        $game = $processor->process($game, $character);
        $this->assertEquals(Game::STATUS_SUCCESS, $game->getStatus());
        $this->assertEquals(6, $game->getTriesLeft());
    }

    /**
     * Tests a failing game.
     */
    public function testGameFail()
    {
        $game = new Game();
        $game->setWord('a');
        $game->setTriesLeft(1);
        $character = 'b';

        $em = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $em->expects($this->once())
            ->method('flush');

        $processor = new GameProcessor($em);
        $game = $processor->process($game, $character);
        $this->assertEquals(Game::STATUS_FAIL, $game->getStatus());
        $this->assertEquals(0, $game->getTriesLeft());
    }

    /**
     * Tests a busy game.
     */
    public function testGameBusy()
    {
        $game = new Game();
        $game->setWord('something');
        $game->setTriesLeft(11);
        $game->setCharactersGuessed(array('s', 'o', 'm'));
        $character = 'z';

        $em = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $em->expects($this->once())
            ->method('flush');

        $processor = new GameProcessor($em);
        $game = $processor->process($game, $character);
        $this->assertEquals(10, $game->getTriesLeft());
        $this->assertEquals(array('s', 'o', 'm', 'z'), $game->getCharactersGuessed());
    }

    /**
     * Tests game not found.
     *
     * @expectedException \Hangman\Bundle\ApiBundle\Exception\GameNotFoundException
     */
    public function testGameNotFound()
    {
        $character = '-';

        $em = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $em->expects($this->never())
            ->method('flush');

        $processor = new GameProcessor($em);
        $processor->process(null, $character);
    }

    /**
     * Tests supplying an invalid character.
     * @expectedException \Hangman\Bundle\ApiBundle\Exception\InvalidCharacterException
     */
    public function testInvalidCharacter()
    {
        $game = new Game();
        $character = '-';

        $em = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $em->expects($this->never())
            ->method('flush');

        $processor = new GameProcessor($em);
        $processor->process($game, $character);
    }

    /**
     * Tests a game that has no more tries left.
     *
     * @expectedException \Hangman\Bundle\ApiBundle\Exception\TriesDepletedException
     */
    public function testGameNoMoreTries()
    {
        $game = new Game();
        $game->setWord('something');
        $game->setTriesLeft(0);
        $character = 'a';

        $em = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $em->expects($this->never())
            ->method('flush');

        $processor = new GameProcessor($em);
        $processor->process($game, $character);
    }

    /**
     * Tests supplying a character that was already tried before.
     *
     * @expectedException \Hangman\Bundle\ApiBundle\Exception\CharacterNotNewException
     */
    public function testGameRetryCharacter()
    {
        $game = new Game();
        $game->setWord('something');
        $game->setCharactersGuessed(array('a'));
        $character = 'a';

        $em = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $em->expects($this->never())
            ->method('flush');

        $processor = new GameProcessor($em);
        $processor->process($game, $character);
    }
}
