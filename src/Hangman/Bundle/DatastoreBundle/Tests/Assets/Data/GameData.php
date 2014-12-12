<?php
namespace Hangman\Bundle\DatastoreBundle\Tests\Assets\Data;

use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;

class GameData implements DataInterface
{
    /**
     * @return array
     */
    public function getData()
    {
        return array(
            array(
                'id' => 1,
                'tries_left' => 11,
                'word' => 'hangman',
                'status' => Game::STATUS_BUSY
            ),
            array(
                'id' => 2,
                'tries_left' => 8,
                'word' => 'rocks',
                'status' => Game::STATUS_BUSY
            ),
            array(
                'id' => 3,
                'tries_left' => 0,
                'word' => 'sorry',
                'status' => Game::STATUS_FAIL
            ),
            array(
                'id' => 4,
                'tries_left' => 5,
                'word' => 'perfect',
                'status' => Game::STATUS_SUCCESS
            )
        );
    }
} 