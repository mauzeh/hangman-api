<?php

namespace Hangman\Bundle\ApiBundle;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;

/**
 * Class Processor
 *
 * @package Hangman\Bundle\DatastoreBundle
 * @todo Move into ApiBundle?
 */
class GameProcessor
{
    private $em = null;

    public function __construct(EntityManagerInterface $em)
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
    public function process(View $view, Game $game = null, $character = null)
    {
        // A corresponding Game must have been found
        if ($game === null) {
            $view->setStatusCode(404);

            return $view;
        }

        // Exactly one character may be submitted
        if (!preg_match('/^[a-z]{1}$/i', $character)) {
            $view->setStatusCode(400);

            return $view;
        }

        $triesLeft = $game->getTriesLeft();

        // Already done!
        if ($game->getStatus() === Game::STATUS_SUCCESS) {
            $view->setStatusCode(200);
            $view->setData($game);

            return $view;
        }

        // Already done!
        if ($game->getStatus() === Game::STATUS_FAIL) {
            $view->setStatusCode(200);
            $view->setData($game);

            return $view;
        }

        if ($triesLeft > 11) {
            // @todo return something evil. Bad HTTP code and a message.
            // This is something that really should not ever happen.
            $view->setStatusCode(403);
            $view->setData($game);

            return $view;
        }

        if ($triesLeft == 0) {
            // @todo return an error message? See how the Twitter API does it.
            $view->setStatusCode(403);
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

        $game->addCharacterGuessed($character);

        // If the character does not exist in the word
        if (false === strstr($game->getWord(), $character)) {

            $game->setTriesLeft($game->getTriesLeft() - 1);

            if ($game->getTriesLeft() == 0) {
                $game->setStatus(Game::STATUS_FAIL);
            }

        // If this character was guessed correctly
        } else {

            // If all characters have been guessed correctly
            $pattern = '/['.implode($game->getCharactersGuessed()).']+/';
            $remaining = preg_replace($pattern, '', $game->getWord());

            if (strlen($remaining) === 0) {
                $game->setStatus(Game::STATUS_SUCCESS);
            }
        }

        $view->setStatusCode(200);
        $view->setData($game);

        // @todo ensure this is called in a test
        $this->em->flush();

        return $view;
    }
}
