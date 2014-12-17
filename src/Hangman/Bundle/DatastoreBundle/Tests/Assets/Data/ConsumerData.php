<?php

namespace Hangman\Bundle\DatastoreBundle\Tests\Assets\Data;

use Hangman\Bundle\DatastoreBundle\Entity\ORM\Consumer;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ConsumerData
 *
 * @package Hangman\Bundle\DatastoreBundle\Tests\Assets\Data
 */
class ConsumerData implements FixtureInterface
{
    /**
     * @return array
     */
    public static function getData()
    {
        return array(
            array(
                'token' => 'my-token',
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (static::getData() as $item) {
            $consumer = new Consumer();
            $consumer->setToken($item['token']);
            $manager->persist($consumer);
        }
        $manager->flush();
    }
} 