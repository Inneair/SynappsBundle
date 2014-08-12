<?php

namespace Inneair\SynappsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * Default entity repository that provides additional services over Doctrine's entity repository.
 */
class DefaultEntityRepository extends EntityRepository
{
    /**
     * Wildcards used in SQL 'LIKE' expressions, that must be escaped from any value.
     * @var string
     */
    const LIKE_EXPR_WILDCARDS = '\\%_';

    /**
     * Finds a single entity by a unique property (case-insensitive).
     *
     * @param string $property Property name.
     * @param string $value Unique value.
     * @return object The entity, or <code>null</code> if no entity was found.
     */
    public function findOneByCaseInsensitive($property, $value)
    {
        $queryBuilder = $this->createQueryBuilder('e');
        $expressionBuilder = $queryBuilder->expr();
        $queryBuilder->where($expressionBuilder->eq(
            $expressionBuilder->lower('e.' . $property),
            $expressionBuilder->lower(':value')
        ));
        $queryBuilder->setParameter('value', $value);
        //'%' . addcslashes($value, self::LIKE_EXPR_WILDCARDS) . '%');
        $query = $queryBuilder->getQuery();
        try {
            $query->useQueryCache(true);
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }
}
