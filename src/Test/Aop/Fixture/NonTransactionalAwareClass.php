<?php

namespace Inneair\SynappsBundle\Test\Aop\Fixture;

/**
 * A class that does not support the @Transaction annotation. 
 */
class NonTransactionalAwareClass
{
    /**
     * A public method.
     */
    public function publicMethod()
    {
    }
}
