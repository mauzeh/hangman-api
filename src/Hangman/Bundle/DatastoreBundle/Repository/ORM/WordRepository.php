<?php

namespace Hangman\Bundle\DatastoreBundle\Repository\ORM;

use RuntimeException;
use Doctrine\ORM\EntityRepository;

/**
 * Class WordRepository
 *
 * @package Hangman\Bundle\DatastoreBundle\Repository\ORM
 */
class WordRepository extends EntityRepository
{
    /**
     * @return array
     */
    public function getRandomWord()
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $maxId = $connection->query('SELECT MAX(id) as max_id FROM word')->fetchColumn();
        $sql = "SELECT w.word FROM word AS w WHERE id = :id";

        $result = $connection->prepare($sql)->execute(array('id' => rand(1, $maxId)))->fetch();

        if (false === $result) {
            throw new RuntimeException('No words available');
        }

        return $result['word'];
    }
}
