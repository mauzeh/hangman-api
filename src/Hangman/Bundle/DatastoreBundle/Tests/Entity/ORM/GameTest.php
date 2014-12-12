<?php
namespace Hangman\Bundle\DatastoreBundle\Tests\Entity\ORM;

use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;
use PHPUnit_Framework_TestCase;

class GameTest extends PHPUnit_Framework_TestCase
{
    public function testIfDefaultStatusIsBusy()
    {
        $entity = new Game();

        $this->assertSame(
            Game::STATUS_BUSY,
            $entity->getStatus()
        );
    }

    public function testIfSuccessStatusIsAllowed()
    {
        $entity = new Game();

        $entity->setStatus(Game::STATUS_SUCCESS);

        $this->assertSame(
            Game::STATUS_SUCCESS,
            $entity->getStatus()
        );
    }

    public function testIfBusyStatusIsAllowed()
    {
        $entity = new Game();

        $entity->setStatus(Game::STATUS_BUSY);

        $this->assertSame(
            Game::STATUS_BUSY,
            $entity->getStatus()
        );
    }

    public function testIfFailedStatusIsAllowed()
    {
        $entity = new Game();
        
        $entity->setStatus(Game::STATUS_FAIL);

        $this->assertSame(
            Game::STATUS_FAIL,
            $entity->getStatus()
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testIfInvalidStatusIsNotAllowed()
    {
        $entity = new Game();
        $entity->setStatus('invalid');
    }
} 