<?php

namespace Inneair\SynappsBundle\Entity;

use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Default entity repository that provides additional services over Doctrine's entity repository.
 */
interface EntityRepositoryInterface extends ObjectRepository
{
    /**
     * Add an entity to the repository.
     *
     * @param object $entity Entity to add.
     * @param bool $flush Flush the unit of work (default to <code>false</code>). This may be required if the new entity
     * ID, or the ID of an internal relation is required before the end of the transaction.
     * @return object The added entity.
     */
    public function add($entity, $flush = false);

    /**
     * Update an entity to the repository.
     *
     * @param object $entity Entity to update.
     * @param bool $flush Flush the unit of work (default to <code>false</code>). This may be required if the ID of an
     * internal relation is required before the end of the transaction.
     * @return object The updated entity.
     */
    public function update($entity, $flush = false);

    /**
     * Delete an entity to the repository.
     *
     * @param int $id Entity ID.
     */
    public function delete($id);

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
