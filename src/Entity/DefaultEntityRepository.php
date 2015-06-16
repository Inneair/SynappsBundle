<?php

namespace Inneair\SynappsBundle\Entity;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Inneair\Synapps\Sql\Helper;

/**
 * Default entity repository that provides additional services over Doctrine's entity repository.
 */
class DefaultEntityRepository extends EntityRepository implements EntityRepositoryInterface
{
    /**
     * Entity alias for DQL queries.
     * @var string
     */
    const ENTITY_ALIAS = 'e';

    /**
     * {@inheritDoc}
     */
    public function add($entity, $flush = false)
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush($entity);
        }
        return $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function update($entity, $flush = false)
    {
        $entity = $this->_em->merge($entity);
        if ($flush) {
            $this->_em->flush($entity);
        }
        return $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function delete($id, $flush = false)
    {
        $entity = $this->_em->getReference($this->getEntityName(), $id);
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush($entity);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function findOneByCaseInsensitive($property, $value)
    {
        $queryBuilder = $this->createQueryBuilder(static::ENTITY_ALIAS);
        $expressionBuilder = $queryBuilder->expr();
        $queryBuilder->where($expressionBuilder->eq(
            $expressionBuilder->lower(static::ENTITY_ALIAS . '.' . $property),
            $expressionBuilder->lower(':value')
        ));
        $queryBuilder->setParameter('value', $value);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritDoc}
     */
    public function findGreatestIndex($property, $prefix)
    {
        $queryBuilder = $this->createQueryBuilder(static::ENTITY_ALIAS);
        $expressionBuilder = $queryBuilder->expr();
        $queryBuilder->select($expressionBuilder->max($expressionBuilder->substring(
            static::ENTITY_ALIAS . '.' . $property,
            mb_strlen($prefix) + 1
        )));
        $queryBuilder->where($expressionBuilder->like(
            static::ENTITY_ALIAS . '.' . $property,
            $expressionBuilder->literal(Helper::escapeLikePattern($prefix) . Helper::LIKE_ANY_STRING_WILDCARD)
        ));
        try {
            return $queryBuilder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
        } catch (NoResultException $e) {
            return null;
        }
    }
}
