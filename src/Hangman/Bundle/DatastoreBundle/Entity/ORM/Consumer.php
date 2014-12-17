<?php

namespace Hangman\Bundle\DatastoreBundle\Entity\ORM;

use Doctrine\ORM\Mapping as ORM;

/**
 * The Consumer entity class.
 *
 * @ORM\Entity(repositoryClass="Hangman\Bundle\DatastoreBundle\Repository\ORM\ConsumerRepository")
 * @ORM\Table(name="consumer")
 */
class Consumer
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="token", type="string")
     */
    protected $token;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Consumer
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }
}
