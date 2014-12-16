<?php

namespace Hangman\Bundle\ApiBundle\Tests\Controller;

use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;
use Hangman\Bundle\DatastoreBundle\Tests\Assets\Data\WordData;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->loadFixtures(array(
            'Hangman\Bundle\DataStoreBundle\Tests\Assets\Data\WordData',
        ));
    }

    public function testGameAction()
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

        $client->request('PUT', '/games/'.$game->id, array(
            'character' => 'r',
        ));
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));

        // Assert that we get a valid JSON object back
        $this->assertObjectHasAttribute('id', $game);
        $this->assertObjectHasAttribute('word', $game);
        $this->assertObjectHasAttribute('tries_left', $game);
        $this->assertObjectHasAttribute('characters_guessed', $game);
        $this->assertObjectHasAttribute('status', $game);
    }

    public function testNonExistentGame()
    {
        $client = static::createClient();

        $client->request('PUT', '/games/non-existent', array(
            'character' => 'y',
        ));
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
}
