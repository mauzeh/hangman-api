<?php

namespace Hangman\Bundle\ApiBundle\Tests;

use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    public function testGetGamesAction()
    {
        $client = static::createClient();

        $client->request('GET', '/games');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));

        $expected = array(
            array(
                'tries_left' => 9,
                'word' => 'horse',
                'status' => Game::STATUS_BUSY,
                'characters_guessed' => array(
                    'h', 'o',
                ),
            ),
            array(
                'tries_left' => 9,
                'word' => 'horse',
                'status' => Game::STATUS_BUSY,
                'characters_guessed' => array(
                    'h', 'o',
                ),
            ),
        );
        $actual = json_decode($response->getContent());
        $this->assertEquals($expected, $actual);
    }

    public function testPostGamesAction()
    {
        $client = static::createClient();

        $client->request('POST', '/games/');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $game = json_decode($response->getContent());

        $expected = array(
            'tries_left' => 9,
            'word' => 'horse',
            'status' => Game::STATUS_BUSY,
            'characters_guessed' => array(
                'h', 'o',
            ),
        );
        $this->assertEquals($expected, $game);
    }

    public function testPutGameBusyAction()
    {
        $client = static::createClient();

        $client->request('POST', '/games/');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $game = json_decode($response->getContent());
        $expected = array(
            'tries_left' => 9,
            'word' => 'horse',
            'status' => Game::STATUS_BUSY,
            'characters_guessed' => array(
                'h', 'o',
            ),
        );
        $this->assertEquals($expected, $game);

        $client->request('PUT', '/games/'.$game->getId(), array(
            'character' => 'r',
        ));
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));

        $expected = array(
            'tries_left' => 8,
            'word' => 'horse',
            'status' => Game::STATUS_BUSY,
            'characters_guessed' => array(
                'h', 'o', 'r',
            ),
        );

        $this->assertEquals($expected, json_decode($response->getContent()));
    }

    public function testPutGameSuccessAction()
    {
        $client = static::createClient();

        $client->request('POST', '/games/');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $game = json_decode($response->getContent());
        $expected = array(
            'tries_left' => 1,
            'word' => 'abcdefghijk',
            'status' => Game::STATUS_BUSY,
            'characters_guessed' => array(
                'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
            ),
        );
        $this->assertEquals($expected, $game);

        $client->request('PUT', '/games/'.$game->getId(), array(
            'character' => 'a',
        ));
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));

        $expected = array(
            'tries_left' => 0,
            'word' => 'abcdefghijk',
            'status' => Game::STATUS_SUCCESS,
            'characters_guessed' => array(
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
            ),
        );

        $this->assertEquals($expected, json_decode($response->getContent()));
    }

    public function testPutGameFailAction()
    {
        $client = static::createClient();

        $client->request('POST', '/games/');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));
        $game = json_decode($response->getContent());
        $expected = array(
            'tries_left' => 1,
            'word' => 'a',
            'status' => Game::STATUS_BUSY,
            'characters_guessed' => array('z'),
        );
        $this->assertEquals($expected, $game);

        $client->request('PUT', '/games/'.$game->getId(), array(
            'character' => 'y',
        ));
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('content-type'));

        $expected = array(
            'tries_left' => 0,
            'word' => 'a',
            'status' => Game::STATUS_FAIL,
            'characters_guessed' => array('z', 'y'),
        );

        $this->assertEquals($expected, json_decode($response->getContent()));
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
