<?php

namespace Hangman\Bundle\ApiBundle\Controller;

// @todo remove unused use statements throughout app

use FOS\RestBundle\View\View;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        $word = $this->getDoctrine()->getManager()
            ->getRepository('HangmanDatastoreBundle:ORM\Word')
            ->getRandomWord();

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
        $game = $this->getDoctrine()->getManager()
            ->getRepository('HangmanDatastoreBundle:ORM\Game')
            ->find($id);

        $character = $request->request->get('character');

        return $this
            ->get('hangman_datastore.processor')
            ->process($game, $character);
    }
}
