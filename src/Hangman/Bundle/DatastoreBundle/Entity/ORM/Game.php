<?php

namespace Hangman\Bundle\DatastoreBundle\Entity\ORM;

use InvalidArgumentException;
use Doctrine\ORM\Mapping as ORM;

/**
 * The Game entity class.
 *
 * @ORM\Entity(repositoryClass="Hangman\Bundle\DatastoreBundle\Repository\ORM\GameRepository")
 * @ORM\Table(name="game")
 */
class Game
{
    const STATUS_BUSY = 'busy';

    const STATUS_FAIL = 'fail';

    const STATUS_SUCCESS = 'success';

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="tries_left", type="integer")
     *
     * @todo Convert to CamelCase format to adhere to Sf2 coding standard
     */
    protected $tries_left = 11;

    /**
     * @ORM\Column(name="word", type="string")
     */
    protected $word;

    /**
     * @ORM\Column(name="status", type="string")
     */
    protected $status = self::STATUS_BUSY;

    /**
     * @ORM\Column(name="characters_guessed", type="json_array")
     */
    protected $charactersGuessed = array();

    /**
     * @param $character
     */
    public function addCharacterGuessed($character)
    {
        if(!in_array($character, $this->charactersGuessed)) {
            $this->charactersGuessed[] = $character;
        }
    }

    /**
     * @return mixed
     */
    public function getCharactersGuessed()
    {
        return $this->charactersGuessed;
    }

    /**
     * @param mixed $charactersGuessed
     */
    public function setCharactersGuessed($charactersGuessed)
    {
        $this->charactersGuessed = $charactersGuessed;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        if (!in_array($status, array(self::STATUS_BUSY, self::STATUS_FAIL, self::STATUS_SUCCESS))) {
            throw new InvalidArgumentException("Invalid status");
        }
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param integer $tries_left
     */
    public function setTriesLeft($tries_left)
    {
        $this->tries_left = $tries_left;
    }

    /**
     * @return integer
     */
    public function getTriesLeft()
    {
        return $this->tries_left;
    }

    /**
     * @param string $word
     */
    public function setWord($word)
    {
        $this->word = $word;
    }

    /**
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }
}