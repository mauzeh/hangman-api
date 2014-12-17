<?php

namespace Hangman\Bundle\ApiBundle;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;

/**
 * Class GameProcessor
 *
 * @package Hangman\Bundle\DatastoreBundle
 */
class GameProcessor
{
    const ERROR_GAME_NOT_FOUND = 'errorGameNotFound';
    const ERROR_INVALID_CHARACTER = 'errorInvalidCharacter';
    const ERROR_TRIES_DEPLETED = 'errorTriesDepleted';
    const ERROR_CHARACTER_NOT_NEW = 'errorCharacterNotNew';

    /**
     * @var array
     */
    private $errors = array(
        self::ERROR_GAME_NOT_FOUND => array(
            'http-code' => 404,
            'message' => 'Sorry, that game does not exist',
        ),
        self::ERROR_INVALID_CHARACTER => array(
            'http-code' => 400,
            'message' => 'Sorry, that was an invalid character',
        ),
        self::ERROR_TRIES_DEPLETED => array(
            'http-code' => 403,
            'message' => 'Sorry, there are no more tries left on this game',
        ),
        self::ERROR_CHARACTER_NOT_NEW => array(
            'http-code' => 403,
            'message' => 'Sorry, you already used that character',
        ),
    );

    /**
     * @var EntityManagerInterface
     */
    private $em;

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
    protected function raiseError(View $view, $code)
    {
        $data = array(
            'errors' => array(
                array(
                    'code' => $code,
                    'message' => $this->errors[$code]['message']
                ),
            ),
        );
        $view->setData($data);
        $view->setStatusCode($this->errors[$code]['http-code']);

        return $view;
    }

    /**
     * Validates that the right preconditions have been met to process the Game.
     *
     * @param View $view
     * @param Game $game
     * @param null $character
     * @return array|bool|View
     */
    protected function validate(View $view, Game $game = null, $character = null)
    {
        // A corresponding Game must have been found
        if ($game === null) {
            return $this->raiseError($view, self::ERROR_GAME_NOT_FOUND);
        }

        // Exactly one character may be submitted
        if (!preg_match('/^[a-z]{1}$/i', $character)) {
            return $this->raiseError($view, self::ERROR_INVALID_CHARACTER);
        }

        // Already done!
        if ($game->getStatus() === Game::STATUS_SUCCESS || $game->getStatus() === Game::STATUS_FAIL) {
            $view->setStatusCode(200);
            $view->setData($game);

            return $view;
        }

        if ($game->getTriesLeft() == 0) {
            return $this->raiseError($view, self::ERROR_TRIES_DEPLETED);
        }

        if (in_array($character, $game->getCharactersGuessed())) {
            return $this->raiseError($view, self::ERROR_CHARACTER_NOT_NEW);
        }

        return true;
    }

    /**
     * Processes a game entry submission.
     *
     * @param View   $view
     * @param Game   $game
     * @param string $character
     *
     * @return View
     */
    public function process(View $view, Game $game = null, $character = null)
    {
        $result = $this->validate($view, $game, $character);
        if (true !== $result) {
            return $result;
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

        $this->em->flush();

        return $view;
    }
}
