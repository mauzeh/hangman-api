<?php
namespace Hangman\Bundle\DatastoreBundle\Tests\Assets\Data;

class WordData implements DataInterface
{
    /**
     * @return array
     */
    public function getData()
    {
        return array(
            array(
                'id' => 1,
                'word' => 'hangman'
            ),
            array(
                'id' => 2,
                'word' => 'always'
            ),
            array(
                'id' => 3,
                'word' => 'rocks'
            ),
            array(
                'id' => 4,
                'word' => 'this'
            ),
            array(
                'id' => 5,
                'word' => 'world'
            )
        );
    }
} 