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
     * @return array
     */
    public static function getData()
    {
        return array(
            array(
                'word' => 'hangman',
            ),
            array(
                'word' => 'is',
            ),
            array(
                'word' => 'awesome',
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (static::getData() as $item) {
            $word = new Word();
            $word->setWord($item['word']);
            $manager->persist($word);
        }
        $manager->flush();
    }
} 