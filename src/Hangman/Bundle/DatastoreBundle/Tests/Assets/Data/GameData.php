<?php

namespace Hangman\Bundle\DatastoreBundle\Tests\Assets\Data;

use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class GameData
 *
 * @package Hangman\Bundle\DatastoreBundle\Tests\Assets\Data
 */
class GameData implements FixtureInterface
{
    /**
     * @return array
     */
    public static function getData()
    {
        return array(
            array(
                'tries_left' => 11,
                'word' => 'hangman',
                'status' => Game::STATUS_BUSY,
                'characters_guessed' => array(),
            ),
            array(
                'tries_left' => 8,
                'word' => 'rocks',
                'status' => Game::STATUS_BUSY,
                'characters_guessed' => array('r', 'c'),
            ),
            array(
                'tries_left' => 0,
                'word' => 'sorry',
                'status' => Game::STATUS_FAIL,
                'characters_guessed' => array(),
            ),
            array(
                'tries_left' => 5,
                'word' => 'perfect',
                'status' => Game::STATUS_SUCCESS,
                'characters_guessed' => array('p', 'e', 'r', 'f', 'c', 't'),
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (static::getData() as $item) {
            $game = new Game();
            $game->setTriesLeft($item['tries_left']);
            $game->setWord($item['word']);
            $game->setStatus($item['status']);
            $game->setCharactersGuessed($item['characters_guessed']);
            $manager->persist($game);
        }
        $manager->flush();
    }
}
