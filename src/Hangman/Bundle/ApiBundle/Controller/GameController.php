<?php

namespace Hangman\Bundle\ApiBundle\Controller;

use FOS\RestBundle\View\View;
use Hangman\Bundle\ApiBundle\Exception\InvalidTokenException;
use Hangman\Bundle\DatastoreBundle\Entity\ORM\Game;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class GameController
 *
 * @package Hangman\Bundle\ApiBundle\Controller
 */
class GameController extends Controller
{
    /**
     * Create a new game.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Create a new game.",
     *  statusCodes={
     *      200="Returned when successful.",
     *      403="Returned when an invalid authentication token is supplied"
     *  }
     * )
     *
     * @param Request $request
     *
     * @return View
     */
    public function postGamesAction(Request $request)
    {
        $this->authenticate($request);

        $em = $this->getDoctrine()->getManager();

        $word = $em
            ->getRepository('HangmanDatastoreBundle:ORM\Word')
            ->getRandomWord();

        $game = new Game();
        $game->setWord($word);

        $em->persist($game);
        $em->flush();

        return View::create($game, 200);
    }

    /**
     * Perform a guess in an existing game.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Perform a guess in an existing game.",
     *  parameters={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="game id"}
     *  },
     *  statusCodes={
     *      200="Returned when successful.",
     *      403={
     *          "Returned when an invalid authentication token is supplied",
     *          "Returned when an invalid character was submitted.",
     *          "Returned when there are no more tries left.",
     *          "Returned when the submitted character has already been submitted before.",
     *      },
     *      404="Returned when the game was not found"
     *  }
     * )
     *
     * @param Request $request
     * @param int     $id
     *
     * @return View
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
}
