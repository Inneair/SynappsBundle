<?php

namespace Inneair\SynappsBundle\Util;

use Doctrine\Common\Collections\Collection;

/**
 * Class containing helper functions for {@link Collection} management and conversions.
 */
final class CollectionUtils
{
    /**
     * Empty private constructor to prevent erroneous instanciations.
     */
    private function __construct()
    {
    }

    /**
     * Resets all keys in a collection.
     *
     * The new keys in the result collection are integers, starting from 0.
     *
     * @param Collection $collection A collection.
     * @return Collection The same collection with keys resested.
     */
    public static function resetKeys(Collection $collection)
    {
        $values = $collection->getValues();
        $collection->clear();
        foreach ($values as $value) {
            $collection->add($value);
        }
        return $collection;
    }
}
