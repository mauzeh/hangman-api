<?php

namespace Hangman\Bundle\ApiBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;
use Hangman\Bundle\DatastoreBundle\Tests\Assets\Data\WordData;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class GameControllerTest
 *
 * @package Hangman\Bundle\ApiBundle\Tests\Controller
 */
class GameControllerTest extends WebTestCase
{
    /**
     * Initialize the data fixture.
     */
    public function setUp()
    {
        $this->loadFixtures(array(
            'Hangman\Bundle\DataStoreBundle\Tests\Assets\Data\WordData',
            'Hangman\Bundle\DataStoreBundle\Tests\Assets\Data\ConsumerData',
        ), null, 'doctrine', ORMPurger::PURGE_MODE_TRUNCATE);
    }

    /**
     * Test the POST and PUT calls.
     *
     * Detailed tests for various game entry scenarios are performed in the unit
     * test class GameProcessorTest.
     *
     * This test merely ascertains that the proper HTTP responses are returned
     * and that the response contains the game information.
     */
    public function testGameAction()
    {
        $client = static::createClient(array(), array(
            'HTTP_X-Hangman-Token' => 'my-token'
        ));

        $client->request('POST', '/games');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

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

        // Assert that we get a valid object back
        $this->assertObjectHasAttribute('id', $game);
        $this->assertObjectHasAttribute('word', $game);
        $this->assertObjectHasAttribute('tries_left', $game);
        $this->assertObjectHasAttribute('characters_guessed', $game);
        $this->assertObjectHasAttribute('status', $game);
    }

    /**
     * Test a single error response.
     *
     * All edge cases and error codes are tested using the unit test class
     * GameProcessorTest.
     *
     * This test merely ascertains that an error propagates properly in the
     * HTTP response.
     */
    public function testErrorResponse()
    {
        $client = static::createClient(array(), array(
            'HTTP_X-Hangman-Token' => 'my-token'
        ));

        $client->request('PUT', '/games/non-existent', array(
            'character' => 'y',
        ));
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Test that an invalid token results in a 403 Forbidden response.
     */
    public function testInvalidTokenResponse()
    {
        $client = static::createClient(array(), array(
            'HTTP_X-Hangman-Token' => 'invalid'
        ));

        $client->request('POST', '/games');
        $response = $client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * Test an invalid URL.
     */
    public function testInvalidUrlResponse()
    {
        $client = static::createClient(array(), array(
            'HTTP_X-Hangman-Token' => 'invalid'
        ));

        $client->request('POST', '/invalid-url');
        $response = $client->getResponse();
        $this->assertEquals(404, $response->getStatusCode());
    }
}
