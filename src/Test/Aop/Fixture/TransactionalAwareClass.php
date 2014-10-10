<?php

namespace Inneair\SynappsBundle\Test\Aop\Fixture;

use Inneair\SynappsBundle\Annotation\TransactionalAwareInterface;

/**
 * A class supporting the @Transactional annotation.
 */
class TransactionalAwareClass extends NonTransactionalAwareClass implements TransactionalAwareInterface
{
    /**
     * A non-public method that does nothing.
     */
    protected function nonPublicMethod()
    {
    }
}
