<?php

namespace Inneair\SynappsBundle\Test\Util;

use Inneair\SynappsBundle\Util\CollectionUtils;
use Inneair\SynappsBundle\Test\AbstractTest;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class containing test suite for collections utilities.
 */
class CollectionUtilsTest extends AbstractTest
{
    /**
     * Resets keys of an empty collection.
     */
    public function testResetKeysOfEmptyCollection()
    {
        $collection = CollectionUtils::resetKeys(new ArrayCollection());
        $this->assertNotNull($collection);
        $this->assertCount(0, $collection);
    }

    /**
     * Resets keys of a collection with different kinds of keys.
     */
    public function testResetKeys()
    {
        $collection = CollectionUtils::resetKeys(new ArrayCollection([
            'stringkey' => 'stringkey',
            2 => 'integerkey',
            false => 'booleankey'
        ]));
        $this->assertNotNull($collection);
        $this->assertCount(3, $collection);
        $flippedArray = array_flip($collection->toArray());
        $this->assertTrue(is_array($flippedArray));
        $this->assertArrayHasKey('stringkey', $flippedArray);
        $this->assertTrue(is_int($flippedArray['stringkey']));
        $this->assertArrayHasKey('integerkey', $flippedArray);
        $this->assertTrue(is_int($flippedArray['integerkey']));
        $this->assertArrayHasKey('booleankey', $flippedArray);
        $this->assertTrue(is_int($flippedArray['booleankey']));
    }
}
