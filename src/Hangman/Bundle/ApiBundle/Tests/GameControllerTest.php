<?php

namespace Hangman\Bundle\ApiBundle\Tests;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;
use Hangman\Bundle\DatastoreBundle\Tests\Assets\Data\WordData;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->loadFixtures(array(
            // @todo reference to another bundle. Maybe put separate fixture inside ApiBundle?
            'Hangman\Bundle\DatastoreBundle\Tests\Assets\Data\GameData',
            'Hangman\Bundle\DatastoreBundle\Tests\Assets\Data\WordData',
        ), null, 'doctrine', ORMPurger::PURGE_MODE_TRUNCATE);
    }

    public function testNewGameAction()
    {
        $client = static::createClient();

        $client->request('POST', '/games');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        // @todo is the use of json_decode warranted here? is there some
        // format-agnostic way of retrieving the original object being received?
        $game = json_decode($response->getContent());
        $this->assertEquals(11, $game->tries_left);
        $this->assertEquals(Game::STATUS_BUSY, $game->status);
        $this->assertEquals(array(), $game->characters_guessed);

        // Also assert that one of the random words was picked
        $this->assertContains(array('word' => $game->word), WordData::getData());
    }

    public function testPutGameBusyAction()
    {
        $client = static::createClient();

        $client->request('PUT', '/games/1', array(
            'character' => 'r',
        ));
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }

    public function testPutGameSuccessAction()
    {
        $client = static::createClient();

        $client->request('PUT', '/games/1', array(
            'character' => 'a',
        ));
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }

    public function testPutGameFailAction()
    {
        $client = static::createClient();

        $client->request('PUT', '/games/1', array(
            'character' => 'y',
        ));
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }

    public function testAlreadyCompletedGame()
    {
        $this->assertTrue(false);
    }

    public function testNonExistentGame()
    {
        $this->assertTrue(false);
    }

    public function testSameCharacterMoreThanOnce()
    {
        $this->assertTrue(false);
    }
}
