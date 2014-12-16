<?php

namespace Hangman\Bundle\DatastoreBundle\Entity\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * The Word entity class.
 *
 * @ORM\Entity(repositoryClass="Hangman\Bundle\DatastoreBundle\Repository\ORM\WordRepository")
 * @ORM\Table(name="word")
 */
class Word
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="word", type="string")
     */
    protected $word;

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