<?php

namespace Inneair\SynappsBundle\Entity;

/**
 * Default entity repository that provides additional services over Doctrine's entity repository.
 */
interface EntityRepositoryInterface
{
    /**
     * Add a entity to the repository.
     *
     * @param object $entity
     * @param bool $flush
     * @return object
     */
    public function add($entity, $flush = false);

    /**
     * Finds a single entity by a unique property (case-insensitive).
     *
     * @param string $property Property name.
     * @param string $value Unique value.
     * @return object The entity, or <code>null</code> if no entity was found.
     */
    public function findOneByCaseInsensitive($property, $value);

    /**
     * Finds the greatest index used after a given prefix, in a property.
     *
     * The search is performed using case insensitive checks (standard LIKE operator behaviour), between the property
     * values, and the prefix.
     *
     * @param string $property Property name.
     * @param string $prefix Prefix used in values, before the index.
     * @return int The greatest positive index, or <code>null</code> if no index was found.
     */
    public function findGreatestIndex($property, $prefix);
}
