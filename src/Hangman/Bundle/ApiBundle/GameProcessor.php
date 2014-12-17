<?php

namespace Hangman\Bundle\ApiBundle;

use Doctrine\ORM\EntityManagerInterface;
use Hangman\Bundle\ApiBundle\Exception\CharacterNotNewException;
use Hangman\Bundle\ApiBundle\Exception\GameNotFoundException;
use Hangman\Bundle\ApiBundle\Exception\InvalidCharacterException;
use Hangman\Bundle\ApiBundle\Exception\TriesDepletedException;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;

/**
 * Class GameProcessor
 *
 * @package Hangman\Bundle\DatastoreBundle
 */
class GameProcessor
{
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
     * Validates that the right preconditions have been met to process the Game.
     *
     * @param Game $game
     * @param null $character
     *
     * @return array|bool|View
     */
    protected function validate(Game $game = null, $character = null)
    {
        // A corresponding Game must have been found
        if ($game === null) {
            throw new GameNotFoundException(
                404, 'Sorry, that game does not exist'
            );
        }

        // Exactly one character may be submitted
        if (!preg_match('/^[a-z]{1}$/i', $character)) {
            throw new InvalidCharacterException(
                403, 'Sorry, that was an invalid character'
            );
        }

        // No more tries left.
        if ($game->getTriesLeft() === 0) {
            throw new TriesDepletedException(
                403, 'Sorry, there are no more tries left on this game'
            );
        }

        // Character has already been submitted before.
        if (in_array($character, $game->getCharactersGuessed())) {
            throw new CharacterNotNewException(
                403, 'Sorry, you already used that character'
            );
        }
    }

    /**
     * Processes a game entry submission.
     *
     * @param Game   $game
     * @param string $character
     *
     * @return Game
     */
    public function process(Game $game = null, $character = null)
    {
        $this->validate($game, $character);

        // Already done!
        if ($game->getStatus() === Game::STATUS_SUCCESS || $game->getStatus() === Game::STATUS_FAIL) {

            return $game;
        }

        $game->addCharacterGuessed($character);

        // If all characters have been guessed correctly
        $pattern = '/['.implode($game->getCharactersGuessed()).']+/';
        $remaining = preg_replace($pattern, '', $game->getWord());

        if (strlen($remaining) === 0) {
            $game->setStatus(Game::STATUS_SUCCESS);
        }

        // If the character does not exist in the word
        if (false === strstr($game->getWord(), $character)) {

            $game->setTriesLeft($game->getTriesLeft() - 1);

            if ($game->getTriesLeft() == 0) {
                $game->setStatus(Game::STATUS_FAIL);
            }
        }

        $this->em->flush();

        // Replace the word with a censored version that has dots for letters
        // that are not (yet) guessed.
        $patternReverse = '/[^'.implode($game->getCharactersGuessed()).']/';
        $game->setWord(preg_replace($patternReverse, '.', $game->getWord()));

        return $game;
    }
}
