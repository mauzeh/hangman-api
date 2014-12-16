<?php

namespace Hangman\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GameController
 *
 * @package Hangman\Bundle\ApiBundle\Controller
 */
class GameController extends Controller
{
    /**
     * @param Request $request
     *
     * @return View
     */
    public function postGamesAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $word = $em->getRepository('HangmanDatastoreBundle:ORM\Word')->getRandomWord();

        $game = new Game();
        $game->setWord($word);

        return View::create($game);
    }

    /**
     * @param $id
     *
     * @return View
     */
    public function putGameAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $game = $em->getRepository('HangmanDatastoreBundle:ORM\Game')->find($id);
        $character = $request->request['character'];
        $triesLeft = $game->getTriesLeft();

        // Already done!
        if ($game->getStatus() === Game::STATUS_SUCCESS) {
            // @todo return something HTTP like, FRIENDLY
            $response = new Response();
            $response->setStatusCode(200);

            return $response;
        }

        // Already done!
        if ($game->getStatus() === Game::STATUS_FAIL) {
            // @todo return something HTTP like, UNFRIENDLY
            $response = new Response();
            $response->setStatusCode(200);

            return $response;
        }

        if ($triesLeft >= 11) {
            // @todo return something evil. Bad HTTP code and a message.
            // This is something that really should not ever happen.
            $response = new Response();
            $response->setStatusCode(200);

            return $response;
        }

        // If the letter is already in use
        if (in_array($character, $game->getCharactersGuessed())) {
            // @todo return something useful.

        }

        // If the character exists in the word
        if (false !== strstr($game->getWord(), $character)) {

            $game->addCharacterGuessed($character);

        // If the character does not exist in the word
        } else {

            $game->setTriesLeft($game->getTriesLeft() - 1);

        }

        return View::create($game);
    }
}
