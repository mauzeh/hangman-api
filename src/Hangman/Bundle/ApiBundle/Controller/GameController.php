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
        $em = $this->getDoctrine()->getManager();

        $word = $em
            ->getRepository('HangmanDatastoreBundle:ORM\Word')
            ->getRandomWord();

        $game = new Game();
        $game->setWord($word);

        $em->persist($game);
        $em->flush();

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

        $view = View::create();

        return $this
            ->get('hangman_api.processor')
            ->process($view, $game, $character);
    }
}
