<?php

namespace Hangman\Bundle\DatastoreBundle;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;

/**
 * Class Processor
 *
 * @package Hangman\Bundle\DatastoreBundle
 * @todo Move into ApiBundle?
 */
class Processor
{
    private $em = null;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Game $game
     * @param $character
     * @return View
     *
     * @todo break up into smaller pieces.
     */
    public function process(Game $game, $character)
    {
        $view = View::create();

        $triesLeft = $game->getTriesLeft();

        // Already done!
        if ($game->getStatus() === Game::STATUS_SUCCESS) {
            // @todo return something HTTP like, FRIENDLY
            $view->setStatusCode(200);
            $view->setData($game);
            return $view;
        }

        // Already done!
        if ($game->getStatus() === Game::STATUS_FAIL) {
            // @todo return something HTTP like, UNFRIENDLY
            $view->setStatusCode(200);
            $view->setData($game);
            return $view;
        }

        if ($triesLeft >= 11) {
            // @todo return something evil. Bad HTTP code and a message.
            // This is something that really should not ever happen.
            $view->setStatusCode(200);
            $view->setData($game);
            return $view;
        }

        // If the letter is already in use
        if (in_array($character, $game->getCharactersGuessed())) {
            // @todo return something useful.
            $view->setStatusCode(200);
            $view->setData($game);
            return $view;
        }

        // If the character exists in the word
        if (false !== strstr($game->getWord(), $character)) {

            $game->addCharacterGuessed($character);

            // If the character does not exist in the word
        } else {

            $game->setTriesLeft($game->getTriesLeft() - 1);

        }

        $view->setStatusCode(200);
        $view->setData($game);

        $this->em->flush();

        return $view;
    }
}
