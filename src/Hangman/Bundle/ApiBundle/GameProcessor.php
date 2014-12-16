<?php

namespace Hangman\Bundle\ApiBundle;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;

/**
 * Class Processor
 *
 * @package Hangman\Bundle\DatastoreBundle
 */
class GameProcessor
{
    const ERROR_GAME_NOT_FOUND = '100';
    const ERROR_INVALID_CHARACTER = '200';
    const ERROR_TRIES_DEPLETED = '300';
    const ERROR_CHARACTER_NOT_NEW = '300';

    private $errorMessages = array(
        self::ERROR_GAME_NOT_FOUND => 'Sorry, that game does not exist',
        self::ERROR_INVALID_CHARACTER => 'Sorry, that was an invalid character',
        self::ERROR_TRIES_DEPLETED => 'Sorry, there are no more tries left on this game',
        self::ERROR_CHARACTER_NOT_NEW => 'Sorry, you already used that character',
    );

    private $em = null;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Generates a properly formatted error.
     *
     * @param int $code The error code for which to generate a formatted error.
     *
     * @return array The formatted error.
     */
    protected function generateError($code)
    {
        return array(
            'errors' => array(
                array(
                    'code' => $code,
                    'message' => $this->errorMessages[$code]
                ),
            ),
        );
    }

    /**
     * Processes a game entry submission.
     *
     * @param View   $view
     * @param Game   $game
     * @param string $character
     *
     * @return View
     *
     * @todo break up into smaller pieces.
     */
    public function process(View $view, Game $game = null, $character = null)
    {
        // A corresponding Game must have been found
        if ($game === null) {
            $view->setData($this->generateError(self::ERROR_GAME_NOT_FOUND));
            $view->setStatusCode(404);

            return $view;
        }

        // Exactly one character may be submitted
        if (!preg_match('/^[a-z]{1}$/i', $character)) {
            $view->setData($this->generateError(self::ERROR_INVALID_CHARACTER));
            $view->setStatusCode(400);

            return $view;
        }

        // Already done!
        if ($game->getStatus() === Game::STATUS_SUCCESS || $game->getStatus() === Game::STATUS_FAIL) {
            $view->setStatusCode(200);
            $view->setData($game);

            return $view;
        }

        if ($game->getTriesLeft() == 0) {
            $view->setData($this->generateError(self::ERROR_TRIES_DEPLETED));
            $view->setStatusCode(403);

            return $view;
        }

        if (in_array($character, $game->getCharactersGuessed())) {
            $view->setData($this->generateError(self::ERROR_CHARACTER_NOT_NEW));
            $view->setStatusCode(403);

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
