<?php

namespace Inneair\SynappsBundle\Entity;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Inneair\Synapps\Sql\Helper;

/**
 * Default entity repository that provides additional services over Doctrine's entity repository.
 */
class DefaultEntityRepository extends EntityRepository
{
    /**
     * Entity alias for DQL queries.
     * @var string
     */
    const ENTITY_ALIAS = 'e';

    /**
     * Finds a single entity by a unique property (case-insensitive).
     *
     * @param string $property Property name.
     * @param string $value Unique value.
     * @return object The entity, or <code>null</code> if no entity was found.
     */
    public function findOneByCaseInsensitive($property, $value)
    {
        $queryBuilder = $this->createQueryBuilder(self::ENTITY_ALIAS);
        $expressionBuilder = $queryBuilder->expr();
        $queryBuilder->where($expressionBuilder->eq(
            $expressionBuilder->lower(self::ENTITY_ALIAS . '.' . $property),
            $expressionBuilder->lower(':value')
        ));
        $queryBuilder->setParameter('value', $value);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

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
    public function findGreatestIndex($property, $prefix)
    {
        $queryBuilder = $this->createQueryBuilder(self::ENTITY_ALIAS);
        $expressionBuilder = $queryBuilder->expr();
        $queryBuilder->select($expressionBuilder->max($expressionBuilder->substring(
            self::ENTITY_ALIAS . '.' . $property,
            mb_strlen($prefix) + 1
        )));
        $queryBuilder->where($expressionBuilder->like(
            self::ENTITY_ALIAS . '.' . $property,
            $expressionBuilder->literal(Helper::escapeLikePattern($prefix) . Helper::LIKE_ANY_STRING_WILDCARD)
        ));
        try {
            return $queryBuilder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
        } catch (NoResultException $e) {
            return null;
        }
    }
}
