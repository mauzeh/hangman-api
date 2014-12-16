<?php

namespace Hangman\Bundle\DatastoreBundle\Tests\Assets\Data;

use Hangman\Bundle\DatastoreBundle\Entity\ORM\Word;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class WordData
 *
 * @package Hangman\Bundle\DatastoreBundle\Tests\Assets\Data
 */
class WordData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $data = array(
            array(
                'word' => 'hangman',
            ),
            array(
                'word' => 'always',
            ),
            array(
                'word' => 'rocks',
            ),
            array(
                'word' => 'this',
            ),
            array(
                'word' => 'world',
            ),
        );
        foreach ($data as $item) {
            $word = new Word();
            $word->setWord($item['word']);
            $manager->persist($word);
        }
        $manager->flush();
    }
} 