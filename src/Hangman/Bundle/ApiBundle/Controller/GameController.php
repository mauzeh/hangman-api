<?php

namespace Hangman\Bundle\ApiBundle\Controller;

// @todo optimize imports throughout app

use FOS\RestBundle\View\View;
use Hangman\Bundle\ApiBundle\Exception\InvalidTokenException;
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
     * Simple authentication mechanism.
     *
     * Normally, on more elaborate APIs, we would want to use an implementation
     * of Symfony's SimplePreAuthenticatorInterface. However, given the
     * simplicity of this API, this method suffices for now.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function authenticate(Request $request)
    {
        $token = $request->headers->get('X-Hangman-Token');
        $consumer = $this->getDoctrine()->getManager()
            ->getRepository('HangmanDatastoreBundle:ORM\Consumer')
            ->findByToken($token);

        if (empty($consumer)) {
            throw new InvalidTokenException(
                403, 'Invalid authentication token.'
            );
        }
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function postGamesAction(Request $request)
    {
        $view = View::create();

        $this->authenticate($request);

        $em = $this->getDoctrine()->getManager();

        $word = $em
            ->getRepository('HangmanDatastoreBundle:ORM\Word')
            ->getRandomWord();

        $game = new Game();
        $game->setWord($word);

        $em->persist($game);
        $em->flush();

        return $view->setData($game);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return mixed
     */
    public function putGameAction(Request $request, $id)
    {
        $this->authenticate($request);

        $game = $this->getDoctrine()->getManager()
            ->getRepository('HangmanDatastoreBundle:ORM\Game')
            ->find($id);

        $character = $request->request->get('character');

        $game = $this
            ->get('hangman_api.processor')
            ->process($game, $character);

        return View::create($game, 200);
    }
}
